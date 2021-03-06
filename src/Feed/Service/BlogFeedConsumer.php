<?php
namespace Acelaya\Website\Feed\Service;

use Acelaya\Website\Feed\BlogOptions;
use Acelaya\Website\Feed\GuzzleClient;
use Acelaya\ZsmAnnotatedServices\Annotation\Inject;
use Doctrine\Common\Cache;
use Zend\Feed\Reader\Feed\FeedInterface;
use Zend\Feed\Reader\Http\ClientInterface;
use Zend\Feed\Reader\Reader;

class BlogFeedConsumer implements BlogFeedConsumerInterface
{
    /**
     * @var Cache\CacheProvider
     */
    private $feedCache;
    /**
     * @var BlogOptions
     */
    private $blogOptions;
    /**
     * @var Cache\ClearableCache
     */
    private $templatesCache;

    /**
     * BlogFeedConsumer constructor.
     * @param ClientInterface $httpClient
     * @param Cache\Cache $feedCache
     * @param Cache\ClearableCache $templatesCache
     * @param BlogOptions $blogOptions
     *
     * @Inject({GuzzleClient::class, "Acelaya\Website\FeedCache", Cache\Cache::class, BlogOptions::class})
     */
    public function __construct(
        ClientInterface $httpClient,
        Cache\Cache $feedCache,
        Cache\ClearableCache $templatesCache,
        BlogOptions $blogOptions
    ) {
        Reader::setHttpClient($httpClient);
        $this->feedCache = $feedCache;
        $this->blogOptions = $blogOptions;
        $this->templatesCache = $templatesCache;
    }

    public function refreshFeed(): array
    {
        $cacheId = $this->blogOptions->getCacheKey();
        $feed = Reader::import($this->blogOptions->getFeed());
        $feed = $this->processFeed($feed);

        // If no feed has been cached yet, cache current one and return
        if (! $this->feedCache->contains($cacheId)) {
            $this->templatesCache->deleteAll();
            $this->feedCache->save($cacheId, $feed);
            return $feed;
        }

        // Check if the last feed has changed, otherwise, return
        $cachedFeed = $this->feedCache->fetch($cacheId);
        if ($cachedFeed[0]['link'] === $feed[0]['link']) {
            return $feed;
        }

        // If the feed has changed, clear all cached elements so that views are refreshed, and cache feed too
        $this->templatesCache->deleteAll();
        $this->feedCache->save($cacheId, $feed);
        return $feed;
    }

    /**
     * @param FeedInterface $feed
     * @return array
     */
    protected function processFeed(FeedInterface $feed): array
    {
        $data = [];
        $count = 0;

        /** @var FeedInterface $entry */
        foreach ($feed as $entry) {
            if ($count >= $this->blogOptions->getElementsToDisplay()) {
                break;
            }

            $data[] = [
                'title' => $entry->getTitle(),
                'link' => $entry->getLink(),
            ];
            $count += 1;
        }

        return $data;
    }
}
