<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

class EnhancedRSSDiscoverer {
    private $cacheFile = 'discovered_feeds.json';
    private $statsFile = 'feed_stats.json';
    private $feedCacheTime = 300; // 5 minutes for fresher content

private function getKnownFeeds() {
    return [
        // News
        [
            'url' => 'http://rss.cnn.com/rss/cnn_topstories.rss',
            'category' => 'news',
            'name' => 'CNN Top Stories'
        ],
        [
            'url' => 'https://feeds.bbci.co.uk/news/rss.xml',
            'category' => 'news',
            'name' => 'BBC News'
        ],
        [
            'url' => 'https://feeds.reuters.com/reuters/topNews',
            'category' => 'news',
            'name' => 'Reuters Top News'
        ],
        
        // Technology
        [
            'url' => 'http://rss.cnn.com/rss/cnn_tech.rss',
            'category' => 'technology',
            'name' => 'CNN Technology'
        ],
        [
            'url' => 'https://feeds.feedburner.com/TechCrunch',
            'category' => 'technology',
            'name' => 'TechCrunch'
        ],
        [
            'url' => 'https://www.wired.com/feed/rss',
            'category' => 'technology',
            'name' => 'Wired'
        ],
        [
            'url' => 'https://www.theverge.com/rss/index.xml',
            'category' => 'technology',
            'name' => 'The Verge'
        ],
        
        // Science
        [
            'url' => 'https://www.sciencedaily.com/rss/all.xml',
            'category' => 'science',
            'name' => 'Science Daily'
        ],
        [
            'url' => 'https://www.nature.com/nature.rss',
            'category' => 'science',
            'name' => 'Nature'
        ],
        
        // Business
        [
            'url' => 'https://feeds.bloomberg.com/markets/news.rss',
            'category' => 'business',
            'name' => 'Bloomberg Markets'
        ],
        [
            'url' => 'https://www.forbes.com/innovation/feed/',
            'category' => 'business',
            'name' => 'Forbes Innovation'
        ],
        
        // Gaming
        [
            'url' => 'https://www.gamespot.com/feeds/news',
            'category' => 'gaming',
            'name' => 'GameSpot'
        ],
        [
            'url' => 'https://www.ign.com/rss/articles',
            'category' => 'gaming',
            'name' => 'IGN'
        ],

        // Entertainment
        [
            'url' => 'http://feeds.feedburner.com/variety/headlines',
            'category' => 'entertainment',
            'name' => 'Variety'
        ],
        [
            'url' => 'https://deadline.com/feed',
            'category' => 'entertainment',
            'name' => 'Deadline Hollywood'
        ],

        // Health
        [
            'url' => 'https://www.medicalnewstoday.com/newsfeeds-rss',
            'category' => 'health',
            'name' => 'Medical News Today'
        ],
        [
            'url' => 'https://www.webmd.com/rss/all.xml',
            'category' => 'health',
            'name' => 'WebMD'
        ],
        
        // Sports
        [
            'url' => 'https://www.espn.com/espn/rss/news',
            'category' => 'sports',
            'name' => 'ESPN'
        ],
        [
            'url' => 'https://api.foxsports.com/v1/rss',
            'category' => 'sports',
            'name' => 'Fox Sports'
        ]
    ];
}

    public function discoverAndProcess() {
        try {
            if ($this->isCacheValid()) {
                $this->updateStats('cache_hits');
                $cached = $this->loadCache();
                return json_encode($cached);
            }

            $discoveredFeeds = [];
            $stats = $this->loadStats();

            foreach ($this->getKnownFeeds() as $feedInfo) {
                $feeds = $this->processFeed($feedInfo);
                if ($feeds) {
                    $discoveredFeeds = array_merge($discoveredFeeds, $feeds);
                    $this->updateStats('feeds_processed', $feedInfo['name']);
                }
            }

            if (!empty($discoveredFeeds)) {
                $this->cacheResults($discoveredFeeds);
            }

            $this->updateStats('total_items', count($discoveredFeeds));
            
            $response = [
                'feeds' => $discoveredFeeds,
                'stats' => $this->loadStats()
            ];

            return json_encode($response);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function processFeed($feedInfo) {
        try {
            $content = $this->fetchUrl($feedInfo['url']);
            if (!$content) return null;

            $items = [];
            
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($content);
            
            if (isset($xml->channel)) {
                $count = 0;
                foreach ($xml->channel->item as $item) {
                    if ($count++ >= 10) break;
                    
                    $feedItem = $this->formatFeedItem($item, $feedInfo);
                    if ($feedItem) {
                        $items[] = $feedItem;
                    }
                }
            }

            return $items;
        } catch (Exception $e) {
            error_log("Error processing feed {$feedInfo['url']}: " . $e->getMessage());
            return null;
        }
    }

    private function formatFeedItem($item, $feedInfo) {
        try {
            $content = isset($item->{'content:encoded'}) ? 
                (string)$item->{'content:encoded'} : 
                (string)$item->description;

            return [
                'title' => $this->cleanContent((string)$item->title),
                'content' => $this->cleanContent($content),
                'link' => (string)$item->link,
                'pubDate' => (string)$item->pubDate,
                'source' => $feedInfo['name'],
                'category' => $feedInfo['category']
            ];
        } catch (Exception $e) {
            error_log("Error formatting feed item: " . $e->getMessage());
            return null;
        }
    }

    private function fetchUrl($url) {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept: application/rss+xml, application/xml',
                ],
                'timeout' => 15,
                'follow_location' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];

        $context = stream_context_create($opts);
        return @file_get_contents($url, false, $context);
    }

    private function cleanContent($content) {
        $content = strip_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content);
        return trim($content);
    }

    private function loadStats() {
        if (file_exists($this->statsFile)) {
            return json_decode(file_get_contents($this->statsFile), true);
        }
        return [
            'feeds_processed' => [],
            'total_items' => 0,
            'cache_hits' => 0,
            'last_update' => date('Y-m-d H:i:s')
        ];
    }

    private function updateStats($key, $value = null) {
        $stats = $this->loadStats();
        if ($key === 'feeds_processed' && $value) {
            if (!isset($stats['feeds_processed'][$value])) {
                $stats['feeds_processed'][$value] = 0;
            }
            $stats['feeds_processed'][$value]++;
        } elseif ($key === 'cache_hits') {
            $stats['cache_hits']++;
        } elseif ($key === 'total_items') {
            $stats['total_items'] = $value;
        }
        $stats['last_update'] = date('Y-m-d H:i:s');
        file_put_contents($this->statsFile, json_encode($stats));
    }

    private function isCacheValid() {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        return (time() - filemtime($this->cacheFile)) < $this->feedCacheTime;
    }

    private function loadCache() {
        $data = json_decode(file_get_contents($this->cacheFile), true);
        return [
            'feeds' => $data,
            'stats' => $this->loadStats()
        ];
    }

    private function cacheResults($feeds) {
        file_put_contents($this->cacheFile, json_encode($feeds));
    }
}

// Main execution
try {
    $discoverer = new EnhancedRSSDiscoverer();
    echo $discoverer->discoverAndProcess();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>