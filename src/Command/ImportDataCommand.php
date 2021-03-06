<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Repository\SerieRepository;
use App\Repository\MovieGenreRepository;
use App\Entity\Movie;
use App\Entity\Serie;
use App\Entity\MovieGenre;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\Descriptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;


class ImportDataCommand extends Command
{
    protected static $defaultName = 'app:import-data';

    /**
     * @var MovieRepository
     */
    private $movieRepo;
    /**
     * @var SerieRepository
     */
    private $serieRepo;
    /**
     * @var MovieGenreRepository
     */
    private $movieGenreRepo;
    /**
     * @var ContainerInterface
     */
    private $container;

    const API_URL = "https://api.themoviedb.org/3/trending/all/week?api_key=5cc2a8649806823dc48b871af14a0f06";
    const API_URL_MOVIE_GENRES = "https://api.themoviedb.org/3/genre/movie/list?language=fr-FR&api_key=5cc2a8649806823dc48b871af14a0f06";

    public function __construct(
        MovieRepository $movieRepository,
        SerieRepository $serieRepository,
        MovieGenreRepository $movieGenreRepository,
        ContainerInterface $container
    ) {
        $this->movieRepo = $movieRepository;
        $this->serieRepo = $serieRepository;
        $this->movieGenreRepo = $movieGenreRepository;
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Commande qui enregistre les films et les séries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $httpClient = HttpClient::create();
        $responseContent = json_decode($httpClient->request('GET', self::API_URL)->getContent());

        /**
         * @var EntityManager $em
         */
        $em = $this->container->get('doctrine')->getManager();

        $nbMoviesCreated = 0;
        $nbSeriesCreated = 0;
        foreach ($responseContent->results as $r) {
            if ($r->media_type === 'movie') {
                $movie = $this->movieRepo->findOneBy(['imdbID' => $r->id]);
                //Si on ne trouve pas le film par son identifiant IMDB
                if (!$this->movieRepo->findOneBy(['imdbID' => $r->id])) {
                    // Création d'un film
                    $movie = new Movie();

                    $movie->setTitle($r->title);
                    $movie->setImage('https://image.tmdb.org/t/p/w500' . $r->backdrop_path);
                    $movie->setNote($r->vote_average);
                    $movie->setReleaseDate(new \DateTime($r->release_date));
                    $movie->setImdbID($r->id);
                    $movie->setOverview($r->overview);
                    $movie->setMediaType($r->media_type);


                    

                    // Incrémentation du compteur
                    $nbMoviesCreated++;
                }

                if (count($r->genre_ids) > 0 && count($movie->getGenre()) === 0) {

                    foreach ($r->genre_ids as $genreId) {
                        $genre = $this->movieGenreRepo->findOneBy(['imdbID' => $genreId]);
                        $movie->addGenre($genre);
                    }
                }
                $em->persist($movie);
            }
            if ($r->media_type === 'tv') {
                //Si on ne trouve pas le film par son identifiant IMDB
                if (!$this->serieRepo->findOneBy(['imdbID' => $r->id])) {
                    // Création d'un film
                    $serie = new Serie();

                    $serie->setTitle($r->name);
                    $serie->setImage('https://image.tmdb.org/t/p/w500' . $r->backdrop_path);
                    $serie->setNote($r->vote_average);
                    $serie->setReleaseDate(new \DateTime($r->first_air_date));
                    $serie->setImdbID($r->id);
                    $serie->setOverview($r->overview);
                    $serie->setMediaType($r->media_type);



                    $em->persist($serie);

                    // Incrémentation du compteur
                    $nbSeriesCreated++;
                }
            }
        }
        if (count($this->movieGenreRepo->findAll()) === 0) {

            $httpClient = HttpClient::create();
            $responseContent = json_decode($httpClient->request('GET', self::API_URL_MOVIE_GENRES)->getContent());

            foreach ($responseContent->genres as $g) {
                $genre = new MovieGenre();
                $genre->setName($g->name);
                $genre->setImdbID($g->id);
                $em->persist($genre);
               
            }
        }

        $em->flush();

        $io->success($nbMoviesCreated . ' films ont été créés :)');
        $io->success($nbSeriesCreated . ' séries ont été créés :)');

        return 0;
    }
}
