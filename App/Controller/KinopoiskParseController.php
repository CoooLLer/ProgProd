<?php


namespace App\Controller;

use App\Core\Kernel;
use App\Core\View;
use App\Repository\MovieRatingRepository;
use App\Repository\MovieRepository;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;

class KinopoiskParseController
{
    private View $view;
    private MovieRepository $movieRepository;
    private MovieRatingRepository $movieRatingRepository;

    public function __construct()
    {
        $this->view = Kernel::getInstance()->getView();
        $this->movieRepository = new MovieRepository(Kernel::getInstance()->getDatabase());
        $this->movieRatingRepository = new MovieRatingRepository(Kernel::getInstance()->getDatabase());
    }

    public function parseTop()
    {

//        $handle = curl_init();
//        //$url = "https://www.kinopoisk.ru/lists/top250/";
//        $url = "https://www.kinopoisk.ru/top/navigator/m_act[num_vote]/100/m_act[is_film]/on/order/rating/perpage/50/";
//        curl_setopt($handle, CURLOPT_URL, $url);
//        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt ($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36');
//        curl_setopt ($handle, CURLOPT_HEADER, 0);
//        curl_setopt ($handle, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt ($handle, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt ($handle,CURLOPT_CONNECTTIMEOUT,120);
//        curl_setopt ($handle,CURLOPT_TIMEOUT,120);
//        curl_setopt ($handle,CURLOPT_MAXREDIRS,10);
//        //curl_setopt ($handle,CURLOPT_COOKIEFILE,"cookie.txt");
//        //curl_setopt ($handle,CURLOPT_COOKIEJAR,"cookie.txt");
//        curl_setopt($handle, CURLOPT_HTTPHEADER, [
//            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//            'accept-encoding: gzip, deflate, br',
//            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
//            'cache-control: no-cache',
//            'dnt: 1',
//            'pragma: no-cache',
//            'referer: https://www.kinopoisk.ru/showcaptcha?cc=1&retpath=https%3A//www.kinopoisk.ru/lists/top250%3F_cd52cf17a312c732f139e4c0dafb785b&t=0/1590955202/d641f12973490c082ec8aeb10f440014&s=710ead10b957bdf61b07765e9fc88423',
//            'sec-fetch-dest: document',
//            'sec-fetch-mode: navigate',
//            'sec-fetch-site: same-origin',
//            'sec-fetch-user: ?1',
//            'upgrade-insecure-requests: 1',
//            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36'
//        ]);
//        $output = curl_exec($handle);
//        curl_close($handle);


        $crawler = new Crawler(file_get_contents(PROJECT_ROOT . '/fixtures/page1.html'));


        $movies = $crawler->filterXPath('//div[contains(concat(\' \',normalize-space(@class),\' \'),\' desktop-rating-selection-film-item \')]');

        foreach ($movies as $movie) {
            $moviePosition = $movie->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->nodeValue;
            $moviePicture = $movie->childNodes->item(1)->childNodes->item(0)->attributes->getNamedItem('src')->nodeValue;
            $movieData = $movie->childNodes->item(2)->childNodes->item(0);
            $movieName = $movieData->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->nodeValue;
            $movieYear = (int) substr($movieData->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->childNodes->item(1)->nodeValue, -4);
            $movieRatingValue = (float) $movieData->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->nodeValue;
            $movieVotersCount = preg_replace('/[^\d]/', '', $movieData->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->childNodes->item(1)->nodeValue);

            if (!$movie = $this->movieRepository->getOneBy(['name' => $movieName, 'year' => $movieYear])) {
                $movie = $this->movieRepository->add([
                    'name' => $movieName,
                    'year' => $movieYear,
                    'picture' => $moviePicture,
                ]);
            }

            if (!$movieRating = $this->movieRatingRepository->getBy(['movie_id' => $movie->getId(), 'date' => (new DateTime())->format('Y.m.d')])) {
                $movieRating = $this->movieRatingRepository->add([
                    'movie_id' => $movie->getId(),
                    'date' => (new DateTime())->format('Y-m-d 00:00:00'),
                    'rating' => $movieRatingValue,
                    'position' => $moviePosition,
                    'voters_count' => $movieVotersCount,
                ]);
            }

        }


    }
}