<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insight Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Manage Insights</h2>

    <form id="insight-form" class="mb-4" enctype="multipart/form-data">
        <input type="hidden" id="insight_id">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Judul</label>
            <input type="text" id="judul" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Slug</label>
            <input type="text" id="slug" class="w-full p-2 border rounded-lg" readonly>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Isi</label>
            <textarea id="isi" rows="4" class="w-full p-2 border rounded-lg"></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Penulis</label>
            <input type="text" id="penulis" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Upload Gambar</label>
            <input type="file" id="image_file" class="w-full p-2 border rounded-lg">
            <img id="preview_image" class="mt-2 hidden w-32 h-32 object-cover rounded" alt="Image Preview">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Tanggal Terbit</label>
            <input type="date" id="TanggalTerbit" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Kategori</label>
            <select id="category_id" class="w-full p-2 border rounded-lg">
                <option value="">Pilih Kategori</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
        <button type="button" id="reset-form" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-2">Reset</button>
    </form>

    <table class="w-full bg-white shadow-md rounded-lg mt-6">
        <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Judul</th>
            <th class="p-2">Isi</th>
            <th class="p-2">Gambar</th>
            <th class="p-2">Kategori</th>
            <th class="p-2">Penulis</th>
            <th class="p-2">Tanggal Terbit</th>
            <th class="p-2">Aksi</th>
        </tr>
        </thead>
        <tbody id="insight-table-body">
        </tbody>
    </table>
</div>

<script>
    const API_URL = "/api/insights";
    const CATEGORY_URL = "/api/categories";

    document.addEventListener("DOMContentLoaded", function () {
        loadCategories();
        loadInsights();

        document.getElementById("judul").addEventListener("input", function () {
            document.getElementById("slug").value = toSlug(this.value);
        });

        // Image preview
        document.getElementById("image_file").addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = document.getElementById("preview_image");

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove("hidden");
                }

                reader.readAsDataURL(file);
            }
        });

        // Form submission handler
        document.getElementById("insight-form").addEventListener("submit", function(e) {
            e.preventDefault();

            const insightId = document.getElementById("insight_id").value;
            const formData = new FormData();

            formData.append("judul", document.getElementById("judul").value);
            formData.append("slug", document.getElementById("slug").value);
            formData.append("isi", document.getElementById("isi").value);
            formData.append("penulis", document.getElementById("penulis").value);
            formData.append("TanggalTerbit", document.getElementById("TanggalTerbit").value);
            formData.append("category_id", document.getElementById("category_id").value);

            if (document.getElementById("image_file").files[0]) {
                formData.append("image", document.getElementById("image_file").files[0]);
            }

            let url = API_URL;
            let method = "post";

            if (insightId) {
                url = `${API_URL}/${insightId}`;
                formData.append("_method", "PUT"); // Laravel method spoofing
            }

            axios({
                method: method,
                url: url,
                data: formData,
                headers: { "Content-Type": "multipart/form-data" }
            })
                .then(response => {
                    alert(insightId ? "Insight berhasil diupdate" : "Insight berhasil dibuat");
                    resetForm();
                    loadInsights();
                })
                .catch(error => {
                    console.error("Error saving insight:", error);
                    if (error.response && error.response.data && error.response.data.errors) {
                        let errorMessage = "Error:\n";
                        for (const [field, messages] of Object.entries(error.response.data.errors)) {
                            errorMessage += `${field}: ${messages.join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert("Gagal menyimpan insight. Lihat console untuk detail.");
                    }
                });
        });

        // Reset form button
        document.getElementById("reset-form").addEventListener("click", function() {
            resetForm();
        });
    });

    function resetForm() {
        document.getElementById("insight-form").reset();
        document.getElementById("insight_id").value = "";
        document.getElementById("preview_image").classList.add("hidden");
    }

    function toSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter selain huruf, angka, dan spasi
            .replace(/\s+/g, '-') // Ganti spasi dengan tanda "-"
            .replace(/-+/g, '-'); // Hapus tanda "-" berlebih
    }

    function loadCategories() {
        axios.get(CATEGORY_URL)
            .then(response => {
                const categorySelect = document.getElementById("category_id");
                categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
                response.data.forEach(category => {
                    categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                });
            })
            .catch(error => console.error("Error fetching categories:", error));
    }

    function loadInsights() {
        axios.get(API_URL)
            .then(response => {
                const tableBody = document.getElementById("insight-table-body");
                tableBody.innerHTML = "";
                response.data.forEach(insight => {
                    // Truncate content for display
                    const shortIsi = insight.isi.length > 50 ? insight.isi.substring(0, 50) + '...' : insight.isi;

                    tableBody.innerHTML += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2">${insight.judul}</td>
                            <td class="p-2">${shortIsi}</td>
                            <td class="p-2">
                                ${insight.image_url ? `<img src="${insight.image_url}" class="w-16 h-16 object-cover rounded">` : 'No Image'}
                            </td>
                            <td class="p-2">${insight.category_name || '-'}</td>
                            <td class="p-2">${insight.penulis}</td>
                            <td class="p-2">${insight.TanggalTerbit}</td>
                            <td class="p-2">
                                <button onclick="editInsight(${insight.id})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                                <button onclick="deleteInsight(${insight.id})" class="bg-red-500 text-white px-2 py-1 rounded mt-1">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error("Error fetching insights:", error));
    }

    function editInsight(id) {
        axios.get(`${API_URL}/${id}`)
            .then(response => {
                const insight = response.data;
                document.getElementById("insight_id").value = insight.id;
                document.getElementById("judul").value = insight.judul;
                document.getElementById("slug").value = insight.slug;
                document.getElementById("isi").value = insight.isi;
                document.getElementById("penulis").value = insight.penulis;
                document.getElementById("TanggalTerbit").value = insight.TanggalTerbit;
                document.getElementById("category_id").value = insight.category_id || '';

                const preview = document.getElementById("preview_image");
                if (insight.image_url) {
                    preview.src = insight.image_url;
                    preview.classList.remove("hidden");
                } else {
                    preview.classList.add("hidden");
                }
            })
            .catch(error => console.error("Error fetching insight:", error));
    }

    function deleteInsight(id) {
        if (confirm("Apakah Anda yakin ingin menghapus insight ini?")) {
            axios.delete(`${API_URL}/${id}`)
                .then(() => {
                    alert("Insight berhasil dihapus");
                    loadInsights();
                })
                .catch(error => console.error("Error deleting insight:", error));
        }
    }
</script>
</body>
</html>
