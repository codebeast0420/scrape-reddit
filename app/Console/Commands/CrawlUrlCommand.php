<?php
namespace App\Console\Commands;
use App\Models\Product;
use Illuminate\Console\Command;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
class CrawlUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Url:Crawl {url}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl url using panther';
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
        $url = $this->argument('url');
        $_SERVER['PANTHER_NO_HEADLESS'] = false;
        $_SERVER['PANTHER_NO_SANDBOX'] = true;
        try {
            $client = Client::createChromeClient(base_path("drivers/chromedriver"), null, ["port" => 9558]);
            $this->info("Start processing");
            $client->request('GET', $url);
            $crawler = $client->waitFor('.shopee-page-controller');
            $crawler->filter('#main > div > div._193wCc > div.shop-search-page.container > div > div.shop-search-page__right-section > div > div.shop-search-result-view > div > div.col-xs-2-4')->each(function (Crawler $parentCrawler, $i) {
                // DO THIS: specify the parent tag too
                $titleCrawler = $parentCrawler->filter('#main > div > div._193wCc > div.shop-search-page.container > div > div.shop-search-page__right-section > div > div.shop-search-result-view > div > div.col-xs-2-4 div._36CEnF');
                $priceCrawler = $parentCrawler->filter('#main > div > div._193wCc > div.shop-search-page.container > div > div.shop-search-page__right-section > div > div.shop-search-result-view > div > div.col-xs-2-4 ._29R_un');
                $imageCrawler = $parentCrawler->filter('#main > div > div._193wCc > div.shop-search-page.container > div > div.shop-search-page__right-section > div > div.shop-search-result-view > div > div.col-xs-2-4 img.mxM4vG');
                $urlCrawler = $parentCrawler->filter('#main > div > div._193wCc > div.shop-search-page.container > div > div.shop-search-page__right-section > div > div.shop-search-result-view > div > div.col-xs-2-4 a[data-sqe="link"]');
                $product = new Product();
                $product->title = $titleCrawler->getText() ?? "";
                $product->price = $priceCrawler->getText() ?? "";
                $product->product_url = $urlCrawler->getAttribute("href") ?? "";
                $product->image_url = $imageCrawler->getAttribute("src") ?? "";
                $product->save();
                $this->info("Item retrieved and saved");
            });
            $client->quit();
        } catch (\Exception $ex) {
            $this->error("Error: " . $ex->getMessage());
            dd($ex->getMessage());
        } finally {
            $this->info("Finished processing");
            $client->quit();
        }
    }
}