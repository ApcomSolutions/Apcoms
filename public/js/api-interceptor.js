/**
 * API Auth Interceptor - Menangani autentikasi untuk semua permintaan API
 */

// Global API token
let apiToken = null;

// Fungsi untuk mendapatkan token API
async function getApiToken() {
    if (apiToken) return apiToken;

    try {
        // Coba dapatkan token dari storage lokal terlebih dahulu
        const savedToken = localStorage.getItem('api_token');
        if (savedToken) {
            apiToken = savedToken;
            return apiToken;
        }

        // Jika tidak ada token disimpan, buat permintaan untuk mendapatkannya
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            // Gunakan kredensial dari admin yang sudah login (session-based)
            // Tidak perlu mengirim email/password karena kita menggunakan kredensial session yang aktif
        });

        if (!response.ok) throw new Error('Failed to get API token');

        const data = await response.json();
        if (!data.success || !data.data || !data.data.token) {
            throw new Error('Invalid token response');
        }

        apiToken = data.data.token;

        // Simpan token untuk penggunaan berikutnya
        localStorage.setItem('api_token', apiToken);

        return apiToken;
    } catch (error) {
        console.error('Error getting API token:', error);
        return null;
    }
}

// Fungsi fetch dengan autentikasi
async function fetchWithAuth(url, options = {}) {
    const token = await getApiToken();

    // Buat headers baru dengan token auth jika tersedia
    const headers = options.headers || {};

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    // Selalu sertakan CSRF token untuk permintaan non-GET
    if (options.method && options.method !== 'GET') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }
    }

    // Pastikan Accept header adalah 'application/json'
    headers['Accept'] = 'application/json';

    // Lakukan fetch dengan headers yang diperbarui
    return fetch(url, {
        ...options,
        headers,
        credentials: 'same-origin', // Selalu sertakan cookies
    });
}

// Override fungsi fetchInsightStats untuk menggunakan fetchWithAuth
async function fetchInsightStats(insightId) {
    try {
        // Gunakan fetchWithAuth untuk mendapatkan token otomatis
        const response = await fetchWithAuth(`/api/admin/dashboard/insight-stats/${insightId}`);

        if (!response.ok) {
            if (response.status === 404) {
                // Tidak ada statistik yang tersedia, kembalikan default
                return { total_views: 0, avg_read_time: 0 };
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Error fetching stats for insight ${insightId}:`, error);
        return { total_views: 0, avg_read_time: 0 };
    }
}

// Auto-refresh token saat token akan kedaluwarsa
function setupTokenRefresh() {
    // Refresh token setiap 50 menit (token biasanya berlaku 60 menit)
    setInterval(async () => {
        // Hapus token lokal agar mendorong permintaan token baru
        localStorage.removeItem('api_token');
        apiToken = null;

        // Inisiasi permintaan token baru
        await getApiToken();
    }, 50 * 60 * 1000); // 50 menit dalam milidetik
}

// Tambahkan API error handler global
function handleApiError(error, endpoint) {
    console.error(`API Error [${endpoint}]:`, error);

    // Jika error adalah 401 Unauthorized, coba refresh token
    if (error.status === 401) {
        // Hapus token dan coba lagi
        localStorage.removeItem('api_token');
        apiToken = null;

        // Tampilkan pesan untuk user
        alert('Sesi Anda telah berakhir. Silakan muat ulang halaman untuk login kembali.');
    }
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    // Dapatkan token saat halaman dimuat
    getApiToken().then(token => {
        if (token) {
            console.log('API token initialized successfully');
            setupTokenRefresh();
        } else {
            console.warn('Failed to initialize API token');
        }
    });
});

// Patch (monkey patch) fungsi fetch asli untuk insight stats
if (typeof window.originalFetchInsightStats === 'undefined') {
    // Simpan referensi ke fungsi asli jika ada
    if (typeof window.fetchInsightStats === 'function') {
        window.originalFetchInsightStats = window.fetchInsightStats;
    }

    // Override dengan fungsi yang diperbarui
    window.fetchInsightStats = fetchInsightStats;
}
