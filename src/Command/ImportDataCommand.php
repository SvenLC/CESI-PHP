<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Entity\Movie;

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
     * @var ContainerInterface
     */
    private $container;

    const API_URL = "https://api.themoviedb.org/3/trending/all/day?api_key=5cc2a8649806823dc48b871af14a0f06";

    public function __construct(MovieRepository $movieRepository, ContainerInterface $container)
    {
        $this->movieRepo = $movieRepository;
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
        foreach ($responseContent->results as $r) {
            if ($r->media_type === 'movie') {
                //Si on ne trouve pas le film par son identifiant IMDB
                if (!$this->movieRepo->findOneBy(['imdbID' => $r->id])) {                   
                    // Création d'un film
                    $movie = new Movie();

                    $movie->setTitle($r->title);
                    $movie->setImage('https://image.tmdb.org/t/p/w500'.$r->backdrop_path);
                    $movie->setNote($r->vote_average);
                    $movie->setReleaseDate(new \DateTime($r->release_date));
                    $movie->setImdbID($r->id);
                    $movie->setOverview($r->overview);
                

                    $em->persist($movie);
                    
                    // Incrémentation du compteur
                    $nbMoviesCreated++;
                }
            }
        }

        $em->flush();

        $io->success($nbMoviesCreated . ' films ont été créés :)');

        return 0;
    }
}
