<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class EnhancedRSSDiscoverer {
    private $cacheFile = 'discovered_feeds.json';
    private $statsFile = 'feed_stats.json';
    private $feedCacheTime = 300; // 5 minutes for fresher content

private function getKnownFeeds() {
    return [
        // News
        ['url' => 'http://rss.cnn.com/rss/cnn_topstories.rss', 'category' => 'news', 'name' => 'CNN Top Stories'],
        ['url' => 'https://feeds.bbci.co.uk/news/rss.xml', 'category' => 'news', 'name' => 'BBC News'],
        ['url' => 'https://feeds.reuters.com/reuters/topNews', 'category' => 'news', 'name' => 'Reuters Top News'],
        ['url' => 'https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml', 'category' => 'news', 'name' => 'The New York Times'],
        ['url' => 'https://feeds.a.dj.com/rss/WSJcomUSBusiness.xml', 'category' => 'news', 'name' => 'The Wall Street Journal'],

        // Technology
        ['url' => 'http://rss.cnn.com/rss/cnn_tech.rss', 'category' => 'technology', 'name' => 'CNN Technology'],
        ['url' => 'https://feeds.feedburner.com/TechCrunch', 'category' => 'technology', 'name' => 'TechCrunch'],
        ['url' => 'https://www.wired.com/feed/rss', 'category' => 'technology', 'name' => 'Wired'],
        ['url' => 'https://www.theverge.com/rss/index.xml', 'category' => 'technology', 'name' => 'The Verge'],
        ['url' => 'https://www.engadget.com/rss.xml', 'category' => 'technology', 'name' => 'Engadget'],
        ['url' => 'https://gizmodo.com/rss', 'category' => 'technology', 'name' => 'Gizmodo'],

        // Science
        ['url' => 'https://www.sciencedaily.com/rss/all.xml', 'category' => 'science', 'name' => 'Science Daily'],
        ['url' => 'https://www.nature.com/nature.rss', 'category' => 'science', 'name' => 'Nature'],
        ['url' => 'https://www.space.com/feeds/all', 'category' => 'science', 'name' => 'Space.com'],
        ['url' => 'https://phys.org/rss-feed/', 'category' => 'science', 'name' => 'Phys.org'],
        
        // Business
        ['url' => 'https://feeds.bloomberg.com/markets/news.rss', 'category' => 'business', 'name' => 'Bloomberg Markets'],
        ['url' => 'https://www.forbes.com/innovation/feed/', 'category' => 'business', 'name' => 'Forbes Innovation'],
        ['url' => 'https://www.cnbc.com/id/10001147/device/rss/rss.html', 'category' => 'business', 'name' => 'CNBC Business'],
        ['url' => 'https://www.economist.com/finance-and-economics/rss.xml', 'category' => 'business', 'name' => 'The Economist'],
        
        // Gaming
        ['url' => 'https://www.gamespot.com/feeds/news', 'category' => 'gaming', 'name' => 'GameSpot'],
        ['url' => 'https://www.ign.com/rss/articles', 'category' => 'gaming', 'name' => 'IGN'],
        ['url' => 'https://www.polygon.com/rss/index.xml', 'category' => 'gaming', 'name' => 'Polygon'],
        ['url' => 'https://www.eurogamer.net/?format=rss', 'category' => 'gaming', 'name' => 'Eurogamer'],

        // Entertainment
        ['url' => 'http://feeds.feedburner.com/variety/headlines', 'category' => 'entertainment', 'name' => 'Variety'],
        ['url' => 'https://deadline.com/feed', 'category' => 'entertainment', 'name' => 'Deadline Hollywood'],
        ['url' => 'https://www.hollywoodreporter.com/rss/', 'category' => 'entertainment', 'name' => 'The Hollywood Reporter'],
        ['url' => 'https://ew.com/feed/', 'category' => 'entertainment', 'name' => 'Entertainment Weekly'],
        
        // Health
        ['url' => 'https://www.medicalnewstoday.com/newsfeeds-rss', 'category' => 'health', 'name' => 'Medical News Today'],
        ['url' => 'https://www.webmd.com/rss/all.xml', 'category' => 'health', 'name' => 'WebMD'],
        ['url' => 'https://www.health.harvard.edu/blog/feed', 'category' => 'health', 'name' => 'Harvard Health Blog'],
        ['url' => 'https://www.mayoclinic.org/feeds/rss-rssfeeds-0', 'category' => 'health', 'name' => 'Mayo Clinic'],
        
        // Sports
        ['url' => 'https://www.espn.com/espn/rss/news', 'category' => 'sports', 'name' => 'ESPN'],
        ['url' => 'https://api.foxsports.com/v1/rss', 'category' => 'sports', 'name' => 'Fox Sports'],
        ['url' => 'https://bleacherreport.com/rss/home', 'category' => 'sports', 'name' => 'Bleacher Report'],
        ['url' => 'https://www.cbssports.com/rss/', 'category' => 'sports', 'name' => 'CBS Sports'],
		// Cryptocurrency
        ['url' => 'https://cointelegraph.com/rss', 'category' => 'cryptocurrency', 'name' => 'Cointelegraph'],
        ['url' => 'https://www.coindesk.com/feed', 'category' => 'cryptocurrency', 'name' => 'CoinDesk'],
        ['url' => 'https://cryptonews.com/news/feed', 'category' => 'cryptocurrency', 'name' => 'CryptoNews'],
        
        // PubMed
        ['url' => 'https://pubmed.ncbi.nlm.nih.gov/rss/search/1Di1IZzM0R1EGk1Xo2DmAKd3rQFcntFAmERLbq9g9ntQrD5Bx4/?limit=50&utm_campaign=pubmed-2&fc=20210112084501', 'category' => 'pubmed', 'name' => 'PubMed: Latest Research'],
        ['url' => 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/erss.cgi?rss_guid=1TkfDmjf0Snf-m8QgjIlSW7zloe1ygPi_pJ7zaPNsn6q1mD-FW', 'category' => 'pubmed', 'name' => 'PubMed: Trending Articles'],
        
        // Bitcoin/Litecoin News
        ['url' => 'https://bitcoinmagazine.com/.rss/full/', 'category' => 'bitcoin_litecoin', 'name' => 'Bitcoin Magazine'],
        ['url' => 'https://litecoin.com/rss/litecoin-news.xml', 'category' => 'bitcoin_litecoin', 'name' => 'Litecoin News'],
        ['url' => 'https://news.bitcoin.com/feed/', 'category' => 'bitcoin_litecoin', 'name' => 'Bitcoin News'],
        
        // Psychology
        ['url' => 'https://www.psychologytoday.com/us/rss', 'category' => 'psychology', 'name' => 'Psychology Today'],
        ['url' => 'https://digest.bps.org.uk/feed/', 'category' => 'psychology', 'name' => 'The British Psychological Society Research Digest'],
        ['url' => 'https://www.scientificamerican.com/psychology/rss/', 'category' => 'psychology', 'name' => 'Scientific American Mind & Brain'],
        
        // Education
        ['url' => 'https://www.edutopia.org/rss.xml', 'category' => 'education', 'name' => 'Edutopia'],
        ['url' => 'https://www.edsurge.com/news/feed', 'category' => 'education', 'name' => 'EdSurge'],
        ['url' => 'https://www.eschoolnews.com/feed/', 'category' => 'education', 'name' => 'eSchool News'],
        
        // Music
        ['url' => 'https://pitchfork.com/rss/news', 'category' => 'music', 'name' => 'Pitchfork'],
        ['url' => 'https://www.rollingstone.com/music.rss', 'category' => 'music', 'name' => 'Rolling Stone Music'],
        ['url' => 'https://consequenceofsound.net/feed/', 'category' => 'music', 'name' => 'Consequence of Sound'],
        
        // Economics
        ['url' => 'https://www.economist.com/economics/rss.xml', 'category' => 'economics', 'name' => 'The Economist Economics'],
        ['url' => 'https://voxeu.org/feed/recent/rss.xml', 'category' => 'economics', 'name' => 'VoxEU'],
        ['url' => 'https://www.nber.org/rss/new.xml', 'category' => 'economics', 'name' => 'The National Bureau of Economic Research'],
		// Politics
        ['url' => 'https://www.politico.com/rss/politicopicks.xml', 'category' => 'politics', 'name' => 'Politico'],
        ['url' => 'https://thehill.com/rss/syndicator/19110', 'category' => 'politics', 'name' => 'The Hill'],
        ['url' => 'https://fivethirtyeight.com/politics/feed/', 'category' => 'politics', 'name' => 'FiveThirtyEight Politics'],
        
        // Environment
        ['url' => 'https://www.nationalgeographic.com/sitemaps/rss/environment.xml', 'category' => 'environment', 'name' => 'National Geographic Environment'],
        ['url' => 'https://www.treehugger.com/rss.xml', 'category' => 'environment', 'name' => 'Treehugger'],
        ['url' => 'https://grist.org/feed/', 'category' => 'environment', 'name' => 'Grist'],
        
        // Travel
        ['url' => 'https://www.lonelyplanet.com/news/feed/atom/', 'category' => 'travel', 'name' => 'Lonely Planet'],
        ['url' => 'https://www.nationalgeographic.com/travel/top-10/rss.xml', 'category' => 'travel', 'name' => 'National Geographic Travel'],
        ['url' => 'https://www.cntraveler.com/feed/rss', 'category' => 'travel', 'name' => 'Condé Nast Traveler'],
        
        // Food
        ['url' => 'https://www.seriouseats.com/atom.xml', 'category' => 'food', 'name' => 'Serious Eats'],
        ['url' => 'https://www.bonappetit.com/feed/rss', 'category' => 'food', 'name' => 'Bon Appétit'],
        ['url' => 'https://www.epicurious.com/feed/rss', 'category' => 'food', 'name' => 'Epicurious'],
        
        // Art & Design
        ['url' => 'https://www.dezeen.com/feed/', 'category' => 'art_design', 'name' => 'Dezeen'],
        ['url' => 'https://www.designboom.com/feed/', 'category' => 'art_design', 'name' => 'designboom'],
        ['url' => 'https://www.archdaily.com/feed', 'category' => 'art_design', 'name' => 'ArchDaily'],
        
        // Philosophy
        ['url' => 'https://www.philosophynow.org/rss.xml', 'category' => 'philosophy', 'name' => 'Philosophy Now'],
        ['url' => 'https://aeon.co/feed.rss', 'category' => 'philosophy', 'name' => 'Aeon'],
        ['url' => 'https://iai.tv/articles/feed.rss', 'category' => 'philosophy', 'name' => 'The Institute of Art and Ideas'],
        
        // History
        ['url' => 'https://www.historytoday.com/feed/rss.xml', 'category' => 'history', 'name' => 'History Today'],
        ['url' => 'https://www.historyextra.com/feed/', 'category' => 'history', 'name' => 'BBC History Extra'],
        ['url' => 'https://www.smithsonianmag.com/rss/history/', 'category' => 'history', 'name' => 'Smithsonian Magazine History']
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