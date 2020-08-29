<?php declare(strict_types=1);
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Article\Controller;

use Ares\Framework\Controller\BaseController;
use Ares\Article\Entity\Article;
use Ares\Article\Exception\ArticleException;
use Ares\Article\Repository\ArticleRepository;
use Ares\Framework\Model\Adapter\DoctrineSearchCriteria;
use Doctrine\Common\Collections\ArrayCollection;
use Jhg\DoctrinePagination\Collection\PaginatedArrayCollection;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ArticleController
 *
 * @package Ares\Article\Controller
 */
class ArticleController extends BaseController
{
    /*
     * Represents the Value of pinned Articles
     */
    private const IS_PINNED = 1;

    /*
     * Represents the Value of Visible Articles
    */
    private const IS_VISIBLE = 1;

    /**
     * @var ArticleRepository
     */
    private ArticleRepository $articleRepository;

    /**
     * @var DoctrineSearchCriteria
     */
    private DoctrineSearchCriteria $searchCriteria;

    /**
     * NewsController constructor.
     *
     * @param   ArticleRepository       $articleRepository
     * @param   DoctrineSearchCriteria  $searchCriteria
     */
    public function __construct(
        ArticleRepository $articleRepository,
        DoctrineSearchCriteria $searchCriteria
    ) {
        $this->articleRepository = $articleRepository;
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @param   Request   $request
     * @param   Response  $response
     *
     * @param             $args
     *
     * @return Response
     * @throws ArticleException
     * @throws PhpfastcacheSimpleCacheException
     * @throws InvalidArgumentException
     */
    public function article(Request $request, Response $response, $args): Response
    {
        /** @var int $id */
        $id = $args['id'];

        /** @var Article $article */
        $article = $this->articleRepository->get((int)$id);

        if (is_null($article)) {
            throw new ArticleException(__('No specific Article found'), 404);
        }

        return $this->respond(
            $response,
            response()->setData($article)
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws ArticleException
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function pinned(Request $request, Response $response): Response
    {
        $this->searchCriteria
            ->addFilter('pinned', self::IS_PINNED)
            ->addFilter('hidden', self::IS_VISIBLE);

        /** @var ArrayCollection $pinnedArticles */
        $pinnedArticles = $this->articleRepository->getList($this->searchCriteria);

        if ($pinnedArticles->isEmpty()) {
            throw new ArticleException(__('No Pinned Articles found'));
        }

        return $this->respond(
            $response,
            response()->setData($pinnedArticles->toArray())
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @param          $args
     *
     * @return Response
     * @throws ArticleException
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function list(Request $request, Response $response, $args): Response
    {
        /** @var int $page */
        $page = $args['page'];

        /** @var int $resultPerPage */
        $resultPerPage = $args['rpp'];

        $this->searchCriteria->setPage((int)$page)
            ->setLimit((int)$resultPerPage)
            ->addOrder('id', 'DESC');

        /** @var ArrayCollection $pinnedArticles */
        $articles = $this->articleRepository->paginate($this->searchCriteria);

        if ($articles->isEmpty()) {
            throw new ArticleException(__('No Articles were found'), 404);
        }

        return $this->respond(
            $response,
            response()->setData($articles->toArray())
        );
    }
}
