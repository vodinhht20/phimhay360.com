<?php

namespace App\Console\Commands;

use App\Models\Wordpress\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ophim\Crawler\OphimCrawler\Crawler;

class FillFilm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'film:fill-data-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filmBackups = Post::where('post_type', 'post')
            ->where('post_status', 'publish')
            ->select('id', 'post_name')
            ->get();
        // Tạo logger mới với đường dẫn đến file muốn lưu log vào
        $errorLog = new Logger('fill_data_errors');
        $errorLog->pushHandler(new StreamHandler(storage_path('logs/fill_data_errors.log'), Logger::INFO));
        $i = 0;
        $this->warn("Đang bắt đầu chạy | Tổng " . $filmBackups->count() . "  Film");
        foreach($filmBackups as $film) {
            $i++;
            $this->info("#{$i} Dang chay film: {$film->post_name}");
            try {
                Http::post("https://phimhay360.com/admin/plugin/ophim-crawler/crawl", [
                    "excludedCategories" => ['Phim 18+'],
                    "excludedRegions" => [],
                    "excludedType" => [],
                    "fields" => [
                        "episodes", "status", "episode_time", "episode_current", "episode_total", "name", "origin_name", "content", "thumb_url", "poster_url", "trailer_url", "quality", "language", "notify", "showtimes", "publish_year", "is_copyright", "type", "is_shown_in_theater", "actors", "directors", "categories", "regions", "tags", "studios"
                    ],
                    "forceUpdate" => true,
                    "slug" => $film->post_name
                ]);
                $this->info('Xong !!');
            } catch (\Exception $e) {
                $errorLog->error("ID: {$film->id}");
                $this->error("Loi !!");
            }
        }

        $this->info("------------------- Hoan Thanh -----------------");
    }
}
