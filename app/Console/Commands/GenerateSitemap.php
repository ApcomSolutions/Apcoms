<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Category;
use App\Models\NewsCategory;
use App\Models\Insight;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML Sitemap';

    public function handle()
    {
        $this->info('Mulai membuat sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency('weekly'))
            ->add(Url::create('/about')
                ->setPriority(0.8)
                ->setChangeFrequency('monthly'))
            ->add(Url::create('/contact')
                ->setPriority(0.8)
                ->setChangeFrequency('monthly'))
            ->add(Url::create('/insights')
                ->setPriority(0.9)
                ->setChangeFrequency('daily'))
            ->add(Url::create('/news')
                ->setPriority(0.9)
                ->setChangeFrequency('daily'));

        // Tambahkan kategori Insights
        $this->info('Menambahkan kategori insights...');
        foreach (Category::all() as $category) {
            $url = Url::create("/insights/category/{$category->slug}")
                ->setPriority(0.7)
                ->setChangeFrequency('weekly');

            if (!is_null($category->updated_at)) {
                $url->setLastModificationDate(Carbon::parse($category->updated_at));
            }

            $sitemap->add($url);
        }

        // Tambahkan kategori News
        $this->info('Menambahkan kategori news...');
        foreach (NewsCategory::all() as $category) {
            $url = Url::create("/news/category/{$category->slug}")
                ->setPriority(0.7)
                ->setChangeFrequency('weekly');

            if (!is_null($category->updated_at)) {
                $url->setLastModificationDate(Carbon::parse($category->updated_at));
            }

            $sitemap->add($url);
        }

        // Tambahkan setiap artikel Insight
        $this->info('Menambahkan artikel insights...');
        foreach (Insight::all() as $insight) {
            $newSlug = $insight->slug;

            // Perbaikan: News menggunakan kolom 'title', Insight menggunakan 'judul'
            // Pastikan kita mengambil property yang benar
            $oldSlug = '';
            if ($insight->isDirty('judul') && isset($insight->getOriginal()['judul'])) {
                $oldSlug = Str::slug($insight->getOriginal()['judul'], '-');
            }

            // Tambahkan slug baru
            $url = Url::create("/insights/{$newSlug}")
                ->setPriority(0.8)
                ->setChangeFrequency('monthly');

            if (!is_null($insight->updated_at)) {
                $url->setLastModificationDate(Carbon::parse($insight->updated_at));
            }

            $sitemap->add($url);

            // Jika slug berubah, tambahkan slug lama ke sitemap
            if ($oldSlug !== '' && $oldSlug !== $newSlug) {
                $sitemap->add(
                    Url::create("/insights/{$oldSlug}")
                        ->setPriority(0.6)
                        ->setChangeFrequency('yearly')
                );
            }
        }

        // Tambahkan setiap artikel News
        $this->info('Menambahkan artikel news...');
        foreach (News::withoutGlobalScopes()->get() as $news) {
            $newSlug = $news->slug;

            // Perbaikan: News menggunakan kolom 'title', bukan 'judul'
            $oldSlug = '';
            if ($news->isDirty('title') && isset($news->getOriginal()['title'])) {
                $oldSlug = Str::slug($news->getOriginal()['title'], '-');
            }

            // Tambahkan slug baru (hanya tambahkan yang status-nya published)
            if ($news->status === 'published') {
                $url = Url::create("/news/{$newSlug}")
                    ->setPriority(0.8)
                    ->setChangeFrequency('monthly');

                if (!is_null($news->updated_at)) {
                    $url->setLastModificationDate(Carbon::parse($news->updated_at));
                }

                $sitemap->add($url);

                // Jika slug berubah, tambahkan slug lama ke sitemap
                if ($oldSlug !== '' && $oldSlug !== $newSlug) {
                    $sitemap->add(
                        Url::create("/news/{$oldSlug}")
                            ->setPriority(0.6)
                            ->setChangeFrequency('yearly')
                    );
                }
            }
        }

        // Menyimpan sitemap ke file
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap berhasil dibuat di /public/sitemap.xml');
    }
}
