<?php


namespace App\Controller;


use App\Core\Kernel;
use App\Core\View;
use App\Repository\MovieRatingRepository;
use App\Repository\MovieRepository;
use DateTime;

class MovieController
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
    public function showTop()
    {
        $data = [];
        if (!empty($_REQUEST['date'])) {
            $topDate = $_REQUEST['date'];
        } else {
            $topDate = (new DateTime())->format('Y-m-d');
        }
        $data['topDate'] = $topDate;
        $moviesRatings = $this->movieRatingRepository->getBy(['<=date' => $topDate], 'rating', 'DESC', 10);
        foreach ($moviesRatings as $movieRating) {
            $data['movies'][] = [
                'movie' => $this->movieRepository->get($movieRating->getMovieId()),
                'rating' => $movieRating
            ];
        }
        $this->view->render(PROJECT_ROOT . '/templates/movie/movies_list.html.php', $data);
        //$movies = $this->movieRatingRepository->getBy()
    }
}