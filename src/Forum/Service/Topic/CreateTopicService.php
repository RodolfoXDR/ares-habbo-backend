<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Forum\Service\Topic;

use Ares\Forum\Entity\Topic;
use Ares\Forum\Exception\TopicException;
use Ares\Forum\Repository\TopicRepository;
use Ares\Framework\Interfaces\CustomResponseInterface;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class CreateTopicService
 *
 * @package Ares\Forum\Service\Topic
 */
class CreateTopicService
{
    /**
     * @var TopicRepository
     */
    private TopicRepository $topicRepository;

    /**
     * @var Slugify
     */
    private Slugify $slug;

    /**
     * CreateTopicService constructor.
     *
     * @param TopicRepository $topicRepository
     * @param Slugify         $slug
     */
    public function __construct(
        TopicRepository $topicRepository,
        Slugify $slug
    ) {
        $this->topicRepository = $topicRepository;
        $this->slug = $slug;
    }

    /**
     * @param   array  $data
     *
     * @return CustomResponseInterface
     * @throws TopicException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws PhpfastcacheSimpleCacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function execute(array $data)
    {
        $topic = $this->getNewTopic($data);

        /** @var Topic $existingTopic */
        $existingTopic = $this->topicRepository->getOneBy([
           'title' => $topic->getTitle()
        ]);

        if ($existingTopic) {
            throw new TopicException(__('There is already a Topic with that title'));
        }

        /** @var Topic $topic */
        $topic = $this->topicRepository->save($topic);

        return response()
            ->setData($topic);
    }

    /**
     * @param   array  $data
     *
     * @return Topic
     */
    public function getNewTopic(array $data): Topic
    {
        $topic = new Topic();

        return $topic
            ->setTitle($data['title'])
            ->setSlug($this->slug->slugify($data['title']))
            ->setDescription($data['description']);
    }
}
