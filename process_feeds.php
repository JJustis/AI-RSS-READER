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
	        // Military & Defense
        ['url' => 'https://www.defensenews.com/arc/outboundfeeds/rss/?outputType=xml', 'category' => 'military', 'name' => 'Defense News'],
        ['url' => 'https://breakingdefense.com/feed/', 'category' => 'military', 'name' => 'Breaking Defense'],
        ['url' => 'https://www.janes.com/feeds/news', 'category' => 'military', 'name' => 'Janes Defense'],
        ['url' => 'https://www.military.com/rss-feeds/news.xml', 'category' => 'military', 'name' => 'Military.com'],
        ['url' => 'https://smallwarsjournal.com/feed', 'category' => 'military', 'name' => 'Small Wars Journal'],

        // Aviation
        ['url' => 'https://www.avweb.com/feed/', 'category' => 'aviation', 'name' => 'AVweb'],
        ['url' => 'https://www.aopa.org/news/rss/all.xml', 'category' => 'aviation', 'name' => 'AOPA News'],
        ['url' => 'https://www.flightglobal.com/rss/news', 'category' => 'aviation', 'name' => 'FlightGlobal'],
        ['url' => 'https://simpleflying.com/feed/', 'category' => 'aviation', 'name' => 'Simple Flying'],

        // Maritime
        ['url' => 'https://www.maritime-executive.com/rss', 'category' => 'maritime', 'name' => 'Maritime Executive'],
        ['url' => 'https://splash247.com/feed/', 'category' => 'maritime', 'name' => 'Splash 247'],
        ['url' => 'https://gcaptain.com/feed/', 'category' => 'maritime', 'name' => 'gCaptain'],

        // Cybersecurity
        ['url' => 'https://threatpost.com/feed/', 'category' => 'cybersecurity', 'name' => 'Threatpost'],
        ['url' => 'https://www.darkreading.com/rss.xml', 'category' => 'cybersecurity', 'name' => 'Dark Reading'],
        ['url' => 'https://www.bleepingcomputer.com/feed/', 'category' => 'cybersecurity', 'name' => 'Bleeping Computer'],
        ['url' => 'https://krebsonsecurity.com/feed/', 'category' => 'cybersecurity', 'name' => 'Krebs on Security'],

        // Artificial Intelligence
        ['url' => 'https://www.artificialintelligence-news.com/feed/', 'category' => 'ai', 'name' => 'AI News'],
        ['url' => 'https://machinelearningmastery.com/feed/', 'category' => 'ai', 'name' => 'Machine Learning Mastery'],
        ['url' => 'https://www.deeplearning.ai/feed/', 'category' => 'ai', 'name' => 'DeepLearning.AI'],

        // Automotive
        ['url' => 'https://www.autoblog.com/rss.xml', 'category' => 'automotive', 'name' => 'Autoblog'],
        ['url' => 'https://www.motortrend.com/feed/', 'category' => 'automotive', 'name' => 'MotorTrend'],
        ['url' => 'https://www.caranddriver.com/rss/all.xml/', 'category' => 'automotive', 'name' => 'Car and Driver'],

        // Photography
        ['url' => 'https://petapixel.com/feed/', 'category' => 'photography', 'name' => 'PetaPixel'],
        ['url' => 'https://www.dpreview.com/feeds/news.xml', 'category' => 'photography', 'name' => 'DPReview'],
        ['url' => 'https://www.imaging-resource.com/feed/', 'category' => 'photography', 'name' => 'Imaging Resource'],

        // Robotics
        ['url' => 'https://spectrum.ieee.org/robotics/rss', 'category' => 'robotics', 'name' => 'IEEE Spectrum Robotics'],
        ['url' => 'https://robohub.org/feed/', 'category' => 'robotics', 'name' => 'Robohub'],
        ['url' => 'https://www.roboticsbusinessreview.com/feed/', 'category' => 'robotics', 'name' => 'Robotics Business Review'],

        // Agriculture
        ['url' => 'https://www.agweb.com/rss/', 'category' => 'agriculture', 'name' => 'AgWeb'],
        ['url' => 'https://www.agriculture.com/rss/all.rss', 'category' => 'agriculture', 'name' => 'Successful Farming'],
        ['url' => 'https://www.farminguk.com/rss/news.xml', 'category' => 'agriculture', 'name' => 'Farming UK'],

        // Weather
        ['url' => 'https://www.weatherzone.com.au/services/rss.jsp', 'category' => 'weather', 'name' => 'WeatherZone'],
        ['url' => 'https://www.accuweather.com/en/feeds/news/weather-news', 'category' => 'weather', 'name' => 'AccuWeather'],
        ['url' => 'https://www.wunderground.com/news/rss.xml', 'category' => 'weather', 'name' => 'Weather Underground'],

        // DIY & Crafts
        ['url' => 'https://www.instructables.com/rss/', 'category' => 'diy_crafts', 'name' => 'Instructables'],
        ['url' => 'https://makezine.com/feed/', 'category' => 'diy_crafts', 'name' => 'Make: Magazine'],
        ['url' => 'https://www.craftsy.com/feed/', 'category' => 'diy_crafts', 'name' => 'Craftsy'],

        // Architecture
        ['url' => 'https://www.architecturalrecord.com/rss/', 'category' => 'architecture', 'name' => 'Architectural Record'],
        ['url' => 'https://www.architectmagazine.com/feed/', 'category' => 'architecture', 'name' => 'Architect Magazine'],
        ['url' => 'https://www.architecturelab.net/feed/', 'category' => 'architecture', 'name' => 'Architecture Lab'],

        // Fashion
        ['url' => 'https://www.vogue.com/feed', 'category' => 'fashion', 'name' => 'Vogue'],
        ['url' => 'https://www.elle.com/rss/all.xml/', 'category' => 'fashion', 'name' => 'Elle'],
        ['url' => 'https://www.harpersbazaar.com/rss/all.xml/', 'category' => 'fashion', 'name' => 'Harper\'s Bazaar'],

        // Literature
        ['url' => 'https://lithub.com/feed/', 'category' => 'literature', 'name' => 'Literary Hub'],
        ['url' => 'https://www.poetryfoundation.org/feed/poems', 'category' => 'literature', 'name' => 'Poetry Foundation'],
        ['url' => 'https://www.theparisreview.org/feed/', 'category' => 'literature', 'name' => 'The Paris Review'],

        // Career & Jobs
        ['url' => 'https://www.themuse.com/feeds/rss/', 'category' => 'career', 'name' => 'The Muse'],
        ['url' => 'https://www.glassdoor.com/blog/feed/', 'category' => 'career', 'name' => 'Glassdoor Blog'],
        ['url' => 'https://www.linkedin.com/news/rss/articles', 'category' => 'career', 'name' => 'LinkedIn Articles'],
    
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
        ['url' => 'https://www.smithsonianmag.com/rss/history/', 'category' => 'history', 'name' => 'Smithsonian Magazine History'],
		 // Space Exploration
        ['url' => 'https://www.universetoday.com/feed/', 'category' => 'space', 'name' => 'Universe Today'],
        ['url' => 'https://www.nasaspaceflight.com/feed/', 'category' => 'space', 'name' => 'NASA Spaceflight'],
        ['url' => 'https://spaceflightnow.com/feed/', 'category' => 'space', 'name' => 'Spaceflight Now'],
        ['url' => 'https://www.planetary.org/planetary-radio/rss', 'category' => 'space', 'name' => 'Planetary Radio'],

        // Quantum Computing
        ['url' => 'https://quantumcomputingreport.com/feed/', 'category' => 'quantum', 'name' => 'Quantum Computing Report'],
        ['url' => 'https://quantum-journal.org/feed/', 'category' => 'quantum', 'name' => 'Quantum Journal'],
        ['url' => 'https://www.quantamagazine.org/feed/', 'category' => 'quantum', 'name' => 'Quanta Magazine'],

        // Renewable Energy
        ['url' => 'https://reneweconomy.com.au/feed/', 'category' => 'renewable_energy', 'name' => 'RenewEconomy'],
        ['url' => 'https://cleantechnica.com/feed/', 'category' => 'renewable_energy', 'name' => 'CleanTechnica'],
        ['url' => 'https://www.pv-magazine.com/feed/', 'category' => 'renewable_energy', 'name' => 'PV Magazine'],

        // Electric Vehicles
        ['url' => 'https://electrek.co/feed/', 'category' => 'ev', 'name' => 'Electrek'],
        ['url' => 'https://insideevs.com/rss/', 'category' => 'ev', 'name' => 'InsideEVs'],
        ['url' => 'https://www.greencarreports.com/feed/', 'category' => 'ev', 'name' => 'Green Car Reports'],

        // Archaeology
        ['url' => 'https://www.archaeology.org/feed', 'category' => 'archaeology', 'name' => 'Archaeology Magazine'],
        ['url' => 'https://www.ancient-origins.net/rss.xml', 'category' => 'archaeology', 'name' => 'Ancient Origins'],
        ['url' => 'https://www.heritagedaily.com/feed', 'category' => 'archaeology', 'name' => 'Heritage Daily'],

        // Neuroscience
        ['url' => 'https://neurosciencenews.com/feed/', 'category' => 'neuroscience', 'name' => 'Neuroscience News'],
        ['url' => 'https://www.brainfacts.org/rss', 'category' => 'neuroscience', 'name' => 'BrainFacts'],
        ['url' => 'https://mind.scientificamerican.com/rss/', 'category' => 'neuroscience', 'name' => 'Scientific American Mind'],

        // Ocean Science
        ['url' => 'https://www.oceanographicmagazine.com/feed/', 'category' => 'ocean', 'name' => 'Oceanographic'],
        ['url' => 'https://www.whoi.edu/news-insights/feed/', 'category' => 'ocean', 'name' => 'Woods Hole Oceanographic'],
        ['url' => 'https://www.deeperblue.com/feed/', 'category' => 'ocean', 'name' => 'DeeperBlue'],

        // Astronomy
        ['url' => 'https://skyandtelescope.org/feed/', 'category' => 'astronomy', 'name' => 'Sky & Telescope'],
        ['url' => 'https://astronomynow.com/feed/', 'category' => 'astronomy', 'name' => 'Astronomy Now'],
        ['url' => 'https://www.astrobio.net/feed/', 'category' => 'astronomy', 'name' => 'Astrobiology Magazine'],

        // Paleontology
        ['url' => 'https://www.prehistoric-wildlife.com/rss.xml', 'category' => 'paleontology', 'name' => 'Prehistoric Wildlife'],
        ['url' => 'https://blog.everythingdinosaur.co.uk/feed', 'category' => 'paleontology', 'name' => 'Everything Dinosaur'],
        ['url' => 'https://www.paleonews.com/feed/', 'category' => 'paleontology', 'name' => 'Paleo News'],

        // Genomics
        ['url' => 'https://www.genomeweb.com/rss/', 'category' => 'genomics', 'name' => 'GenomeWeb'],
        ['url' => 'https://www.bionews.org.uk/rss/', 'category' => 'genomics', 'name' => 'BioNews'],
        ['url' => 'https://www.genengnews.com/feed/', 'category' => 'genomics', 'name' => 'Genetic Engineering News'],

        // Nanotechnology
        ['url' => 'https://www.nanowerk.com/rss.xml', 'category' => 'nanotech', 'name' => 'Nanowerk'],
        ['url' => 'https://phys.org/rss-feed/nanotechnology-news/', 'category' => 'nanotech', 'name' => 'Phys.org Nanotech'],
        ['url' => 'https://www.azonano.com/feed.aspx', 'category' => 'nanotech', 'name' => 'AZoNano'],

        // Linguistics
        ['url' => 'https://languagelog.ldc.upenn.edu/nll/?feed=rss2', 'category' => 'linguistics', 'name' => 'Language Log'],
        ['url' => 'https://lingoblog.ddo.jp/feed', 'category' => 'linguistics', 'name' => 'Lingoblog'],
        ['url' => 'https://www.languagemagazine.com/feed/', 'category' => 'linguistics', 'name' => 'Language Magazine'],

        // Anthropology
        ['url' => 'https://www.sapiens.org/feed/', 'category' => 'anthropology', 'name' => 'SAPIENS'],
        ['url' => 'https://anthropology.net/feed/', 'category' => 'anthropology', 'name' => 'Anthropology.net'],
        ['url' => 'https://www.anthropology-news.org/feed/', 'category' => 'anthropology', 'name' => 'Anthropology News'],

        // Virtual Reality
        ['url' => 'https://www.roadtovr.com/feed/', 'category' => 'vr', 'name' => 'Road to VR'],
        ['url' => 'https://uploadvr.com/feed/', 'category' => 'vr', 'name' => 'UploadVR'],
        ['url' => 'https://www.vrfocus.com/feed/', 'category' => 'vr', 'name' => 'VRFocus'],
		 // Materials Science
        ['url' => 'https://www.materialstoday.com/rss/', 'category' => 'materials', 'name' => 'Materials Today'],
        ['url' => 'https://www.nature.com/nmat.rss', 'category' => 'materials', 'name' => 'Nature Materials'],
        ['url' => 'https://www.advancedsciencenews.com/category/materials/feed/', 'category' => 'materials', 'name' => 'Advanced Materials News'],

        // Bioinformatics
        ['url' => 'https://www.bioinformatics.org/feed/', 'category' => 'bioinformatics', 'name' => 'Bioinformatics.org'],
        ['url' => 'https://academic.oup.com/bioinformatics/rss', 'category' => 'bioinformatics', 'name' => 'Bioinformatics Journal'],
        ['url' => 'https://www.biomedcentral.com/bmcbioinformatics/rss', 'category' => 'bioinformatics', 'name' => 'BMC Bioinformatics'],

        // Conservation
        ['url' => 'https://news.mongabay.com/feed/', 'category' => 'conservation', 'name' => 'Mongabay'],
        ['url' => 'https://www.conservation.org/blog/rss', 'category' => 'conservation', 'name' => 'Conservation International'],
        ['url' => 'https://www.worldwildlife.org/rss/feed.xml', 'category' => 'conservation', 'name' => 'World Wildlife Fund'],

        // Urban Planning
        ['url' => 'https://www.planetizen.com/feed/news', 'category' => 'urban_planning', 'name' => 'Planetizen'],
        ['url' => 'https://www.citylab.com/feed/', 'category' => 'urban_planning', 'name' => 'CityLab'],
        ['url' => 'https://www.smartcitiesdive.com/feeds/news/', 'category' => 'urban_planning', 'name' => 'Smart Cities Dive'],

        // Particle Physics
        ['url' => 'https://cerncourier.com/feed/', 'category' => 'particle_physics', 'name' => 'CERN Courier'],
        ['url' => 'https://physics.aps.org/feed/', 'category' => 'particle_physics', 'name' => 'Physics Magazine'],
        ['url' => 'https://home.cern/news/feed', 'category' => 'particle_physics', 'name' => 'CERN News'],

        // Mycology
        ['url' => 'https://fungi.com/blogs/feed.atom', 'category' => 'mycology', 'name' => 'Fungi Perfecti'],
        ['url' => 'https://www.fungimag.com/feed/', 'category' => 'mycology', 'name' => 'FUNGI Magazine'],
        ['url' => 'https://www.mushroomexpert.com/feed/', 'category' => 'mycology', 'name' => 'MushroomExpert'],

        // Sustainable Agriculture
        ['url' => 'https://regenerationinternational.org/feed/', 'category' => 'sustainable_ag', 'name' => 'Regeneration International'],
        ['url' => 'https://rodaleinstitute.org/blog/feed/', 'category' => 'sustainable_ag', 'name' => 'Rodale Institute'],
        ['url' => 'https://www.permaculture.co.uk/feeds/news', 'category' => 'sustainable_ag', 'name' => 'Permaculture Magazine'],

        // Biotechnology
        ['url' => 'https://www.biotechniques.com/feed/', 'category' => 'biotech', 'name' => 'BioTechniques'],
        ['url' => 'https://www.biotech-now.org/feed', 'category' => 'biotech', 'name' => 'Biotechnology Innovation Organization'],
        ['url' => 'https://www.nature.com/nbt.rss', 'category' => 'biotech', 'name' => 'Nature Biotechnology'],

        // Digital Art
        ['url' => 'https://www.digitalartsonline.co.uk/rss/', 'category' => 'digital_art', 'name' => 'Digital Arts'],
        ['url' => 'https://www.cgchannel.com/feed/', 'category' => 'digital_art', 'name' => 'CGChannel'],
        ['url' => 'https://www.artstation.com/blogs.rss', 'category' => 'digital_art', 'name' => 'ArtStation Magazine'],

        // Volcanology
        ['url' => 'https://www.volcanodiscovery.com/rss/', 'category' => 'volcanology', 'name' => 'Volcano Discovery'],
        ['url' => 'https://volcano.si.edu/news/rss.xml', 'category' => 'volcanology', 'name' => 'Smithsonian GVP'],
        ['url' => 'https://www.volcanocafe.org/feed/', 'category' => 'volcanology', 'name' => 'Volcano Café'],

        // Nuclear Science
        ['url' => 'https://www.world-nuclear-news.org/rss', 'category' => 'nuclear', 'name' => 'World Nuclear News'],
        ['url' => 'https://www.ne.anl.gov/feed/', 'category' => 'nuclear', 'name' => 'Argonne Nuclear'],
        ['url' => 'https://www.neimagazine.com/rss', 'category' => 'nuclear', 'name' => 'Nuclear Engineering International'],

        // Geology
        ['url' => 'https://www.geologypage.com/feed', 'category' => 'geology', 'name' => 'Geology Page'],
        ['url' => 'https://www.earthmagazine.org/feed/', 'category' => 'geology', 'name' => 'EARTH Magazine'],
        ['url' => 'https://geology.com/feed/', 'category' => 'geology', 'name' => 'Geology.com News'],

        // Marine Engineering
        ['url' => 'https://www.marinelink.com/rss', 'category' => 'marine_eng', 'name' => 'Marine Technology News'],
        ['url' => 'https://www.maritime-executive.com/rss/engineering', 'category' => 'marine_eng', 'name' => 'Maritime Engineering'],
        ['url' => 'https://www.marinelog.com/feed/', 'category' => 'marine_eng', 'name' => 'Marine Log'],

        // Data Science
        ['url' => 'https://www.datasciencecentral.com/feed/', 'category' => 'data_science', 'name' => 'Data Science Central'],
        ['url' => 'https://towardsdatascience.com/feed', 'category' => 'data_science', 'name' => 'Towards Data Science'],
        ['url' => 'https://www.kdnuggets.com/feed', 'category' => 'data_science', 'name' => 'KDnuggets'],

        // Entomology
        ['url' => 'https://entomologytoday.org/feed/', 'category' => 'entomology', 'name' => 'Entomology Today'],
        ['url' => 'https://www.insectscience.org/rss/', 'category' => 'entomology', 'name' => 'Journal of Insect Science'],
        ['url' => 'https://www.butterfliesandmoths.org/rss.xml', 'category' => 'entomology', 'name' => 'Butterflies and Moths'],

        // 3D Printing
        ['url' => 'https://3dprintingindustry.com/feed/', 'category' => '3d_printing', 'name' => '3D Printing Industry'],
        ['url' => 'https://all3dp.com/feed/', 'category' => '3d_printing', 'name' => 'All3DP'],
        ['url' => 'https://www.3ders.org/rss.xml', 'category' => '3d_printing', 'name' => '3ders.org'],

        // Acoustics
        ['url' => 'https://acousticalsociety.org/feed/', 'category' => 'acoustics', 'name' => 'Acoustical Society'],
        ['url' => 'https://www.soundandvibration.com/rss/', 'category' => 'acoustics', 'name' => 'Sound & Vibration'],
        ['url' => 'https://www.acoustics.org/feed/', 'category' => 'acoustics', 'name' => 'Acoustics Today'],
		// Epigenetics
        ['url' => 'https://epigeneticsandchromatin.biomedcentral.com/articles/feed', 'category' => 'epigenetics', 'name' => 'Epigenetics & Chromatin'],
        ['url' => 'https://www.tandfonline.com/feed/rss/kepi20', 'category' => 'epigenetics', 'name' => 'Epigenetics Journal'],
        ['url' => 'https://www.nature.com/nature-epigenetics-and-chromatin.rss', 'category' => 'epigenetics', 'name' => 'Nature Epigenetics'],

        // Molecular Biology
        ['url' => 'https://www.cell.com/molecular-cell/rss', 'category' => 'molecular_biology', 'name' => 'Molecular Cell'],
        ['url' => 'https://www.nature.com/nsmb.rss', 'category' => 'molecular_biology', 'name' => 'Nature Structural & Molecular Biology'],
        ['url' => 'https://molecularbiology.biomedcentral.com/articles/feed', 'category' => 'molecular_biology', 'name' => 'BMC Molecular Biology'],

        // Population Genetics
        ['url' => 'https://www.genetics.org/rss/current.xml', 'category' => 'population_genetics', 'name' => 'Genetics Journal'],
        ['url' => 'https://evolbiol.peerj.com/feed.rss', 'category' => 'population_genetics', 'name' => 'PeerJ Evolutionary Biology'],
        ['url' => 'https://www.nature.com/hdy.rss', 'category' => 'population_genetics', 'name' => 'Heredity Journal'],

        // Genetic Engineering
        ['url' => 'https://www.nature.com/nbt/rss', 'category' => 'genetic_engineering', 'name' => 'Nature Biotechnology'],
        ['url' => 'https://www.genengnews.com/feed/', 'category' => 'genetic_engineering', 'name' => 'Genetic Engineering News'],
        ['url' => 'https://www.synthego.com/blog/rss.xml', 'category' => 'genetic_engineering', 'name' => 'CRISPR News'],

        // Medical Genetics
        ['url' => 'https://www.nature.com/gim.rss', 'category' => 'medical_genetics', 'name' => 'Genetics in Medicine'],
        ['url' => 'https://jmg.bmj.com/rss/current.xml', 'category' => 'medical_genetics', 'name' => 'Journal of Medical Genetics'],
        ['url' => 'https://www.nature.com/ejhg.rss', 'category' => 'medical_genetics', 'name' => 'European Journal of Human Genetics'],

        // Evolutionary Genetics
        ['url' => 'https://www.nature.com/nature-ecology-evolution.rss', 'category' => 'evolutionary_genetics', 'name' => 'Nature Ecology & Evolution'],
        ['url' => 'https://academic.oup.com/mbe/rss', 'category' => 'evolutionary_genetics', 'name' => 'Molecular Biology and Evolution'],
        ['url' => 'https://www.genetics.org/rss/evolution.xml', 'category' => 'evolutionary_genetics', 'name' => 'Evolution'],

        // Gene Therapy
        ['url' => 'https://www.nature.com/gt.rss', 'category' => 'gene_therapy', 'name' => 'Gene Therapy'],
        ['url' => 'https://www.cell.com/molecular-therapy-family/rss', 'category' => 'gene_therapy', 'name' => 'Molecular Therapy'],
        ['url' => 'https://humangenetherapy.home.blog/feed/', 'category' => 'gene_therapy', 'name' => 'Human Gene Therapy News'],

        // Pharmacogenetics
        ['url' => 'https://www.nature.com/tpj.rss', 'category' => 'pharmacogenetics', 'name' => 'The Pharmacogenomics Journal'],
        ['url' => 'https://www.pharmgkb.org/feed/rss', 'category' => 'pharmacogenetics', 'name' => 'PharmGKB'],
        ['url' => 'https://academic.oup.com/pharmacy/rss', 'category' => 'pharmacogenetics', 'name' => 'Clinical Pharmacogenetics'],

        // Developmental Genetics
        ['url' => 'https://onlinelibrary.wiley.com/feed/1521189x', 'category' => 'developmental_genetics', 'name' => 'Genesis'],
        ['url' => 'https://www.nature.com/nature-developmental-biology.rss', 'category' => 'developmental_genetics', 'name' => 'Nature Developmental Biology'],
        ['url' => 'https://dev.biologists.org/rss/current.xml', 'category' => 'developmental_genetics', 'name' => 'Development Journal'],

        // Plant Genetics
        ['url' => 'https://academic.oup.com/jxb/rss', 'category' => 'plant_genetics', 'name' => 'Journal of Experimental Botany'],
        ['url' => 'https://www.plantphysiol.org/rss/current.xml', 'category' => 'plant_genetics', 'name' => 'Plant Physiology'],
        ['url' => 'https://www.nature.com/hortres.rss', 'category' => 'plant_genetics', 'name' => 'Horticulture Research'],

        // Cancer Genetics
        ['url' => 'https://www.nature.com/ng.rss', 'category' => 'cancer_genetics', 'name' => 'Nature Genetics'],
        ['url' => 'https://cancerres.aacrjournals.org/rss/current.xml', 'category' => 'cancer_genetics', 'name' => 'Cancer Research'],
        ['url' => 'https://www.nature.com/onc.rss', 'category' => 'cancer_genetics', 'name' => 'Oncogene'],
		// Major News Networks
        ['url' => 'https://abcnews.go.com/abcnews.go.com/News/feed?id=13046', 'category' => 'major_news', 'name' => 'ABC News'],
        ['url' => 'https://www.nbcnews.com/rss/all', 'category' => 'major_news', 'name' => 'NBC News'],
        ['url' => 'https://www.cbsnews.com/latest/rss/main', 'category' => 'major_news', 'name' => 'CBS News'],
        ['url' => 'https://feeds.foxnews.com/foxnews/latest', 'category' => 'major_news', 'name' => 'Fox News'],
        ['url' => 'https://www.msnbc.com/feed', 'category' => 'major_news', 'name' => 'MSNBC'],

        // International News Agencies
        ['url' => 'http://feeds.reuters.com/reuters/worldNews', 'category' => 'world_news', 'name' => 'Reuters World'],
        ['url' => 'https://apnews.com/rss/world', 'category' => 'world_news', 'name' => 'AP World News'],
        ['url' => 'https://www.afp.com/en/rss.xml', 'category' => 'world_news', 'name' => 'AFP News Agency'],
        ['url' => 'https://feeds.bbci.co.uk/news/world/rss.xml', 'category' => 'world_news', 'name' => 'BBC World'],
        ['url' => 'https://rss.dw.com/rdf/rss-en-world', 'category' => 'world_news', 'name' => 'Deutsche Welle'],

        // Associated Press Categories
        ['url' => 'https://apnews.com/rss/apf-topnews', 'category' => 'ap_news', 'name' => 'AP Top News'],
        ['url' => 'https://apnews.com/rss/apf-usnews', 'category' => 'ap_news', 'name' => 'AP U.S. News'],
        ['url' => 'https://apnews.com/rss/apf-politics', 'category' => 'ap_news', 'name' => 'AP Politics'],
        ['url' => 'https://apnews.com/rss/apf-business', 'category' => 'ap_news', 'name' => 'AP Business'],
        ['url' => 'https://apnews.com/rss/apf-sports', 'category' => 'ap_news', 'name' => 'AP Sports'],

        // Academic Journals
        ['url' => 'https://www.nature.com/nature.rss', 'category' => 'academic', 'name' => 'Nature'],
        ['url' => 'https://science.sciencemag.org/rss/current.xml', 'category' => 'academic', 'name' => 'Science'],
        ['url' => 'https://www.cell.com/cell/current.rss', 'category' => 'academic', 'name' => 'Cell'],
        ['url' => 'https://www.pnas.org/content/early/recent.rss', 'category' => 'academic', 'name' => 'PNAS'],
        ['url' => 'https://www.thelancet.com/rssfeed/lancet_current.xml', 'category' => 'academic', 'name' => 'The Lancet'],

        // Academic News & Resources
        ['url' => 'https://www.insidehighered.com/feed', 'category' => 'academic_news', 'name' => 'Inside Higher Ed'],
        ['url' => 'https://www.chronicle.com/rss', 'category' => 'academic_news', 'name' => 'Chronicle of Higher Education'],
        ['url' => 'https://www.timeshighereducation.com/feed', 'category' => 'academic_news', 'name' => 'Times Higher Education'],
        ['url' => 'https://scholarlykitchen.sspnet.org/feed/', 'category' => 'academic_news', 'name' => 'The Scholarly Kitchen'],
        ['url' => 'https://www.sciencedaily.com/rss/all.xml', 'category' => 'academic_news', 'name' => 'ScienceDaily'],

        // Regional News Networks
        ['url' => 'https://www.aljazeera.com/xml/rss/all.xml', 'category' => 'regional_news', 'name' => 'Al Jazeera'],
        ['url' => 'https://www3.nhk.or.jp/nhkworld/en/news/all.rss', 'category' => 'regional_news', 'name' => 'NHK World'],
        ['url' => 'https://timesofindia.indiatimes.com/rssfeeds/296589292.cms', 'category' => 'regional_news', 'name' => 'Times of India'],
        ['url' => 'https://www.scmp.com/rss/all/rss.xml', 'category' => 'regional_news', 'name' => 'South China Morning Post'],
        ['url' => 'https://www.theaustralian.com.au/feed/', 'category' => 'regional_news', 'name' => 'The Australian'],

        // News Analysis & Commentary
        ['url' => 'https://www.theatlantic.com/feed/all/', 'category' => 'news_analysis', 'name' => 'The Atlantic'],
        ['url' => 'https://www.foreignaffairs.com/rss.xml', 'category' => 'news_analysis', 'name' => 'Foreign Affairs'],
        ['url' => 'https://www.brookings.edu/feed/', 'category' => 'news_analysis', 'name' => 'Brookings Institution'],
        ['url' => 'https://www.cfr.org/rss.xml', 'category' => 'news_analysis', 'name' => 'Council on Foreign Relations'],
        ['url' => 'https://www.project-syndicate.org/rss', 'category' => 'news_analysis', 'name' => 'Project Syndicate'],

        // Financial News Networks
        ['url' => 'https://www.ft.com/rss/home', 'category' => 'financial_news', 'name' => 'Financial Times'],
        ['url' => 'https://www.marketwatch.com/rss/topstories', 'category' => 'financial_news', 'name' => 'MarketWatch'],
        ['url' => 'https://feeds.bloomberg.com/markets/news.rss', 'category' => 'financial_news', 'name' => 'Bloomberg'],
        ['url' => 'https://www.reuters.com/rss/businessNews', 'category' => 'financial_news', 'name' => 'Reuters Business'],
        ['url' => 'https://www.cnbc.com/id/100003114/device/rss/rss.html', 'category' => 'financial_news', 'name' => 'CNBC Markets'],
		 // Stock Market News
        ['url' => 'https://seekingalpha.com/feed', 'category' => 'stock_market', 'name' => 'Seeking Alpha'],
        ['url' => 'https://www.investors.com/news/feed/', 'category' => 'stock_market', 'name' => 'Investor\'s Business Daily'],
        ['url' => 'https://www.fool.com/feed/index', 'category' => 'stock_market', 'name' => 'Motley Fool'],
        ['url' => 'https://www.zacks.com/rss/rss.php', 'category' => 'stock_market', 'name' => 'Zacks Investment Research'],
        ['url' => 'https://www.barrons.com/feed/rss', 'category' => 'stock_market', 'name' => 'Barron\'s'],

        // Market Analysis
        ['url' => 'https://www.tradingview.com/feed/', 'category' => 'market_analysis', 'name' => 'TradingView Blog'],
        ['url' => 'https://www.investing.com/rss/news.rss', 'category' => 'market_analysis', 'name' => 'Investing.com'],
        ['url' => 'https://www.finviz.com/rss.ashx', 'category' => 'market_analysis', 'name' => 'Finviz News'],
        ['url' => 'https://stockcharts.com/articles/rss.xml', 'category' => 'market_analysis', 'name' => 'StockCharts'],
        ['url' => 'https://www.streetinsider.com/feed.php', 'category' => 'market_analysis', 'name' => 'Street Insider'],

        // Technical Analysis
        ['url' => 'https://www.dailyfx.com/feeds/technical', 'category' => 'technical_analysis', 'name' => 'DailyFX Technical'],
        ['url' => 'https://www.forexfactory.com/rss.php', 'category' => 'technical_analysis', 'name' => 'Forex Factory'],
        ['url' => 'https://www.fxstreet.com/rss', 'category' => 'technical_analysis', 'name' => 'FXStreet'],
        ['url' => 'https://www.investopedia.com/feedbuilder/feed/getfeed?feedName=rss_technical', 'category' => 'technical_analysis', 'name' => 'Investopedia Technical'],

        // Sector Analysis
        ['url' => 'https://feeds.benzinga.com/sector', 'category' => 'sector_analysis', 'name' => 'Benzinga Sectors'],
        ['url' => 'https://www.sectorspdr.com/feeds/all', 'category' => 'sector_analysis', 'name' => 'Sector SPDR'],
        ['url' => 'https://www.morningstar.com/sectors/rss', 'category' => 'sector_analysis', 'name' => 'Morningstar Sectors'],
        ['url' => 'https://www.marketbeat.com/rss/sectors/', 'category' => 'sector_analysis', 'name' => 'MarketBeat Sectors'],

        // Options Trading
        ['url' => 'https://www.optionstrategist.com/rss.xml', 'category' => 'options', 'name' => 'Options Strategist'],
        ['url' => 'https://www.schaeffersresearch.com/rss', 'category' => 'options', 'name' => 'Schaeffer\'s Research'],
        ['url' => 'https://www.optionseducation.org/rss/feed.aspx', 'category' => 'options', 'name' => 'Options Education'],
        ['url' => 'https://www.cboe.com/rss/options', 'category' => 'options', 'name' => 'CBOE Options'],

        // ETF Analysis
        ['url' => 'https://www.etf.com/rss.xml', 'category' => 'etf_analysis', 'name' => 'ETF.com'],
        ['url' => 'https://www.etftrends.com/feed/', 'category' => 'etf_analysis', 'name' => 'ETF Trends'],
        ['url' => 'https://www.etfdb.com/rss/', 'category' => 'etf_analysis', 'name' => 'ETFdb.com'],
        ['url' => 'https://www.ishares.com/us/resources/rss', 'category' => 'etf_analysis', 'name' => 'iShares'],

        // Market Indices
        ['url' => 'https://www.spglobal.com/spdji/en/rss/rss-details/', 'category' => 'indices', 'name' => 'S&P Indices'],
        ['url' => 'https://www.nasdaq.com/feed/rss/indices', 'category' => 'indices', 'name' => 'NASDAQ Indices'],
        ['url' => 'https://www.ftse.com/rss/indices', 'category' => 'indices', 'name' => 'FTSE Indices'],
        ['url' => 'https://www.msci.com/rss/index', 'category' => 'indices', 'name' => 'MSCI Indices'],

        // IPO News
        ['url' => 'https://www.iposcoop.com/feed/', 'category' => 'ipo_news', 'name' => 'IPO Scoop'],
        ['url' => 'https://www.renaissancecapital.com/rss/ipos', 'category' => 'ipo_news', 'name' => 'Renaissance Capital'],
        ['url' => 'https://www.nasdaq.com/feed/rss/ipos', 'category' => 'ipo_news', 'name' => 'NASDAQ IPOs'],
        ['url' => 'https://www.nyse.com/rss/new-listings', 'category' => 'ipo_news', 'name' => 'NYSE New Listings'],

        // Market Research
        ['url' => 'https://research.tdameritrade.com/grid/public/research/rss/feeds.asp', 'category' => 'market_research', 'name' => 'TD Ameritrade Research'],
        ['url' => 'https://www.capitaliq.com/rss/news', 'category' => 'market_research', 'name' => 'S&P Capital IQ'],
        ['url' => 'https://www.morningstar.com/feeds/research', 'category' => 'market_research', 'name' => 'Morningstar Research'],
        ['url' => 'https://www.valueline.com/rss/research.aspx', 'category' => 'market_research', 'name' => 'Value Line Research'],

        // Earnings Reports
        ['url' => 'https://www.earningswhispers.com/rss', 'category' => 'earnings', 'name' => 'Earnings Whispers'],
        ['url' => 'https://www.zacks.com/rss/earnings_announcements.php', 'category' => 'earnings', 'name' => 'Zacks Earnings'],
        ['url' => 'https://www.nasdaq.com/feed/rss/earnings', 'category' => 'earnings', 'name' => 'NASDAQ Earnings'],
        ['url' => 'https://www.estimize.com/rss/calendar', 'category' => 'earnings', 'name' => 'Estimize Calendar'],
		// Add these to your existing getKnownFeeds() array

// Ivy League News
['url' => 'https://news.harvard.edu/gazette/feed/', 'category' => 'ivy_league', 'name' => 'Harvard Gazette'],
['url' => 'https://news.yale.edu/rss.xml', 'category' => 'ivy_league', 'name' => 'Yale News'],
['url' => 'https://www.princeton.edu/news/rss.xml', 'category' => 'ivy_league', 'name' => 'Princeton News'],
['url' => 'https://news.cornell.edu/rss', 'category' => 'ivy_league', 'name' => 'Cornell Chronicle'],
['url' => 'https://penntoday.upenn.edu/feed', 'category' => 'ivy_league', 'name' => 'Penn Today'],

// College Sports
['url' => 'https://www.ncaa.com/news/rss.xml', 'category' => 'college_sports', 'name' => 'NCAA News'],
['url' => 'https://collegefootballtalk.nbcsports.com/feed/', 'category' => 'college_sports', 'name' => 'College Football Talk'],
['url' => 'https://www.collegebaseballnation.com/rss', 'category' => 'college_sports', 'name' => 'College Baseball'],
['url' => 'https://www.collegegymnews.com/feed/', 'category' => 'college_sports', 'name' => 'College Gymnastics'],

// Campus Life
['url' => 'https://www.studentaffairs.com/feed/', 'category' => 'campus_life', 'name' => 'Student Affairs'],
['url' => 'https://www.collegexpress.com/feed/', 'category' => 'campus_life', 'name' => 'CollegeXpress'],
['url' => 'https://www.campusreform.org/feed', 'category' => 'campus_life', 'name' => 'Campus Reform'],
['url' => 'https://www.collegeconfidential.com/feed', 'category' => 'campus_life', 'name' => 'College Confidential'],

// College Admissions
['url' => 'https://www.commonapp.org/feed', 'category' => 'admissions', 'name' => 'Common App Updates'],
['url' => 'https://www.collegedata.com/feed', 'category' => 'admissions', 'name' => 'College Data'],
['url' => 'https://professionals.collegeboard.org/feed', 'category' => 'admissions', 'name' => 'College Board'],
['url' => 'https://www.petersons.com/college-search/feed', 'category' => 'admissions', 'name' => 'Petersons'],

// Financial Aid
['url' => 'https://www.fastweb.com/rss', 'category' => 'financial_aid', 'name' => 'Fastweb'],
['url' => 'https://www.finaid.org/feed/', 'category' => 'financial_aid', 'name' => 'FinAid'],
['url' => 'https://www.scholarships.com/feed', 'category' => 'financial_aid', 'name' => 'Scholarships.com'],
['url' => 'https://studentaid.gov/rss', 'category' => 'financial_aid', 'name' => 'Federal Student Aid'],

// College Research
['url' => 'https://www.highereducation.org/feed', 'category' => 'college_research', 'name' => 'Higher Education Research'],
['url' => 'https://www.air.org/rss/higher-education', 'category' => 'college_research', 'name' => 'AIR Education Research'],
['url' => 'https://www.centerforcollegeaffordability.org/feed/', 'category' => 'college_research', 'name' => 'College Affordability'],

// STEM Education
['url' => 'https://www.asee.org/feed', 'category' => 'stem_education', 'name' => 'Engineering Education'],
['url' => 'https://www.nsta.org/rss.xml', 'category' => 'stem_education', 'name' => 'Science Teaching'],
['url' => 'https://www.stemconnector.com/feed/', 'category' => 'stem_education', 'name' => 'STEM Connector'],

// Study Abroad
['url' => 'https://www.goabroad.com/feed', 'category' => 'study_abroad', 'name' => 'Go Abroad'],
['url' => 'https://www.gooverseas.com/feed', 'category' => 'study_abroad', 'name' => 'Go Overseas'],
['url' => 'https://www.studyabroad.com/feed', 'category' => 'study_abroad', 'name' => 'Study Abroad.com'],

// Graduate School
['url' => 'https://www.gradschools.com/feed', 'category' => 'graduate_school', 'name' => 'Grad Schools'],
['url' => 'https://www.petersons.com/graduate-schools/feed', 'category' => 'graduate_school', 'name' => 'Graduate Programs'],
['url' => 'https://www.gograd.org/feed/', 'category' => 'graduate_school', 'name' => 'GoGrad'],

// Academic Conferences
['url' => 'https://www.conferencealerts.com/feed', 'category' => 'academic_conferences', 'name' => 'Conference Alerts'],
['url' => 'https://www.papers-invited.com/feed', 'category' => 'academic_conferences', 'name' => 'Papers Invited'],
['url' => 'https://www.allconferences.com/rss', 'category' => 'academic_conferences', 'name' => 'All Conferences'],

// College Technology
['url' => 'https://campustechnology.com/rss', 'category' => 'college_tech', 'name' => 'Campus Technology'],
['url' => 'https://edtechmagazine.com/higher/rss.xml', 'category' => 'college_tech', 'name' => 'EdTech Magazine'],
['url' => 'https://www.educause.edu/rss', 'category' => 'college_tech', 'name' => 'EDUCAUSE'],

// College Career Services
['url' => 'https://www.naceweb.org/rss/feed.aspx', 'category' => 'career_services', 'name' => 'NACE'],
['url' => 'https://www.vault.com/blogs/feed', 'category' => 'career_services', 'name' => 'Vault Career'],
['url' => 'https://www.collegegrad.com/feed', 'category' => 'career_services', 'name' => 'College Grad Jobs'],

// Student Organizations
['url' => 'https://www.studentleadership.com/feed', 'category' => 'student_orgs', 'name' => 'Student Leadership'],
['url' => 'https://www.naspa.org/rss/feed.aspx', 'category' => 'student_orgs', 'name' => 'NASPA Student Affairs'],
['url' => 'https://www.acui.org/feed/', 'category' => 'student_orgs', 'name' => 'Campus Unions'],
// Add these to your existing getKnownFeeds() array

// Cryptography
['url' => 'https://blog.cryptographyengineering.com/feed/', 'category' => 'cryptography', 'name' => 'Cryptography Engineering'],
['url' => 'https://eprint.iacr.org/rss/rss.xml', 'category' => 'cryptography', 'name' => 'IACR Cryptology'],
['url' => 'https://crypto.stanford.edu/rss/', 'category' => 'cryptography', 'name' => 'Stanford Crypto'],
['url' => 'https://www.schneier.com/feed/atom/', 'category' => 'cryptography', 'name' => 'Schneier on Security'],
['url' => 'https://blog.cloudflare.com/tag/cryptography/rss/', 'category' => 'cryptography', 'name' => 'Cloudflare Crypto'],

// Network Security
['url' => 'https://www.securityweek.com/feed/', 'category' => 'network_security', 'name' => 'Security Week'],
['url' => 'https://nakedsecurity.sophos.com/feed/', 'category' => 'network_security', 'name' => 'Naked Security'],
['url' => 'https://www.darkreading.com/rss_simple.asp', 'category' => 'network_security', 'name' => 'Dark Reading'],
['url' => 'https://www.theregister.com/security/headlines.atom', 'category' => 'network_security', 'name' => 'The Register Security'],

// Programming Languages
['url' => 'https://blog.golang.org/feed.atom', 'category' => 'programming_langs', 'name' => 'Go Blog'],
['url' => 'https://blog.rust-lang.org/feed.xml', 'category' => 'programming_langs', 'name' => 'Rust Blog'],
['url' => 'https://blogs.python.org/feed/', 'category' => 'programming_langs', 'name' => 'Python Blog'],
['url' => 'https://jakearchibald.com/posts.rss', 'category' => 'programming_langs', 'name' => 'JavaScript Blog'],
['url' => 'https://blog.jetbrains.com/kotlin/feed/', 'category' => 'programming_langs', 'name' => 'Kotlin Blog'],

// Web Development
['url' => 'https://css-tricks.com/feed/', 'category' => 'web_dev', 'name' => 'CSS Tricks'],
['url' => 'https://www.smashingmagazine.com/feed/', 'category' => 'web_dev', 'name' => 'Smashing Magazine'],
['url' => 'https://dev.to/feed/', 'category' => 'web_dev', 'name' => 'DEV Community'],
['url' => 'https://web.dev/feed.xml', 'category' => 'web_dev', 'name' => 'Web.dev'],
['url' => 'https://stackoverflow.blog/feed/', 'category' => 'web_dev', 'name' => 'Stack Overflow Blog'],

// Software Architecture
['url' => 'https://martinfowler.com/feed.atom', 'category' => 'software_arch', 'name' => 'Martin Fowler'],
['url' => 'https://www.enterpriseintegrationpatterns.com/rss/eip.rss', 'category' => 'software_arch', 'name' => 'Enterprise Integration'],
['url' => 'https://microservices.io/feed.xml', 'category' => 'software_arch', 'name' => 'Microservices.io'],
['url' => 'https://blog.cleancoder.com/feed.xml', 'category' => 'software_arch', 'name' => 'Clean Coder'],

// DevOps
['url' => 'https://devops.com/feed/', 'category' => 'devops', 'name' => 'DevOps.com'],
['url' => 'https://kubernetes.io/feed.xml', 'category' => 'devops', 'name' => 'Kubernetes Blog'],
['url' => 'https://www.docker.com/blog/feed/', 'category' => 'devops', 'name' => 'Docker Blog'],
['url' => 'https://aws.amazon.com/blogs/devops/feed/', 'category' => 'devops', 'name' => 'AWS DevOps'],
['url' => 'https://cloud.google.com/blog/products/devops-sre/rss', 'category' => 'devops', 'name' => 'Google Cloud DevOps'],

// Computer Networks
['url' => 'https://blog.apnic.net/feed/', 'category' => 'networking', 'name' => 'APNIC Blog'],
['url' => 'https://blog.cloudflare.com/rss/', 'category' => 'networking', 'name' => 'Cloudflare Blog'],
['url' => 'https://www.cisco.com/c/en/us/about/blogs/networking-feed.xml', 'category' => 'networking', 'name' => 'Cisco Networking'],
['url' => 'https://ripe.net/rss/feed.xml', 'category' => 'networking', 'name' => 'RIPE Network'],

// Operating Systems
['url' => 'https://lwn.net/headlines/rss', 'category' => 'operating_systems', 'name' => 'Linux Weekly News'],
['url' => 'https://www.phoronix.com/rss.php', 'category' => 'operating_systems', 'name' => 'Phoronix'],
['url' => 'https://www.freebsd.org/news/feed.xml', 'category' => 'operating_systems', 'name' => 'FreeBSD News'],
['url' => 'https://blog.linuxmint.com/?feed=rss2', 'category' => 'operating_systems', 'name' => 'Linux Mint Blog'],

// Information Security
['url' => 'https://www.infosecurity-magazine.com/rss/news/', 'category' => 'infosec', 'name' => 'Infosecurity Magazine'],
['url' => 'https://www.helpnetsecurity.com/feed/', 'category' => 'infosec', 'name' => 'Help Net Security'],
['url' => 'https://www.securityfocus.com/rss/news.xml', 'category' => 'infosec', 'name' => 'Security Focus'],
['url' => 'https://www.csoonline.com/index.rss', 'category' => 'infosec', 'name' => 'CSO Online'],

// Computer Hardware
['url' => 'https://www.anandtech.com/rss/', 'category' => 'hardware', 'name' => 'AnandTech'],
['url' => 'https://www.tomshardware.com/feeds/all', 'category' => 'hardware', 'name' => 'Tom\'s Hardware'],
['url' => 'https://www.techpowerup.com/rss/', 'category' => 'hardware', 'name' => 'TechPowerUp'],
['url' => 'https://www.servethehome.com/feed/', 'category' => 'hardware', 'name' => 'ServeTheHome'],

// Quantum Computing
['url' => 'https://quantum-computing.ibm.com/feed/', 'category' => 'quantum_computing', 'name' => 'IBM Quantum'],
['url' => 'https://quantumcomputingreport.com/feed/', 'category' => 'quantum_computing', 'name' => 'Quantum Computing Report'],
['url' => 'https://www.dwavesys.com/feed/', 'category' => 'quantum_computing', 'name' => 'D-Wave Systems'],
['url' => 'https://ionq.com/feed', 'category' => 'quantum_computing', 'name' => 'IonQ News'],

// Privacy & Anonymity
['url' => 'https://www.privateinternetaccess.com/blog/feed/', 'category' => 'privacy', 'name' => 'Private Internet Access'],
['url' => 'https://www.torproject.org/feed/blog/', 'category' => 'privacy', 'name' => 'Tor Project'],
['url' => 'https://www.eff.org/rss/updates.xml', 'category' => 'privacy', 'name' => 'EFF Updates'],
['url' => 'https://www.epic.org/feed/', 'category' => 'privacy', 'name' => 'EPIC Privacy'],

// Distributed Systems
['url' => 'https://blog.acolyer.org/feed/', 'category' => 'distributed_systems', 'name' => 'The Morning Paper'],
['url' => 'https://www.allthingsdistributed.com/feed.xml', 'category' => 'distributed_systems', 'name' => 'All Things Distributed'],
['url' => 'https://muratbuffalo.blogspot.com/feeds/posts/default', 'category' => 'distributed_systems', 'name' => 'Distributed Systems Blog'],
['url' => 'https://martin.kleppmann.com/feed.xml', 'category' => 'distributed_systems', 'name' => 'Martin Kleppmann'],

// Compiler Design
['url' => 'https://blog.llvm.org/feed.xml', 'category' => 'compilers', 'name' => 'LLVM Blog'],
['url' => 'https://gcc.gnu.org/rss.xml', 'category' => 'compilers', 'name' => 'GCC News'],
['url' => 'https://devblogs.microsoft.com/cppblog/feed/', 'category' => 'compilers', 'name' => 'C++ Team Blog'],
['url' => 'https://blog.rust-lang.org/inside-rust/feed.xml', 'category' => 'compilers', 'name' => 'Inside Rust'],

// Internet Standards
['url' => 'https://www.ietf.org/blog/feed/', 'category' => 'internet_standards', 'name' => 'IETF Blog'],
['url' => 'https://www.w3.org/blog/feed/', 'category' => 'internet_standards', 'name' => 'W3C Blog'],
['url' => 'https://www.icann.org/en/feed/blogs', 'category' => 'internet_standards', 'name' => 'ICANN Blog'],
['url' => 'https://www.internetsociety.org/feed/', 'category' => 'internet_standards', 'name' => 'Internet Society'],
// Add these to your existing getKnownFeeds() array

// Cryptography
['url' => 'https://blog.cryptographyengineering.com/feed/', 'category' => 'cryptography', 'name' => 'Cryptography Engineering'],
['url' => 'https://eprint.iacr.org/rss/rss.xml', 'category' => 'cryptography', 'name' => 'IACR Cryptology'],
['url' => 'https://crypto.stanford.edu/rss/', 'category' => 'cryptography', 'name' => 'Stanford Crypto'],
['url' => 'https://www.schneier.com/feed/atom/', 'category' => 'cryptography', 'name' => 'Schneier on Security'],
['url' => 'https://blog.cloudflare.com/tag/cryptography/rss/', 'category' => 'cryptography', 'name' => 'Cloudflare Crypto'],

// Network Security
['url' => 'https://www.securityweek.com/feed/', 'category' => 'network_security', 'name' => 'Security Week'],
['url' => 'https://nakedsecurity.sophos.com/feed/', 'category' => 'network_security', 'name' => 'Naked Security'],
['url' => 'https://www.darkreading.com/rss_simple.asp', 'category' => 'network_security', 'name' => 'Dark Reading'],
['url' => 'https://www.theregister.com/security/headlines.atom', 'category' => 'network_security', 'name' => 'The Register Security'],

// Programming Languages
['url' => 'https://blog.golang.org/feed.atom', 'category' => 'programming_langs', 'name' => 'Go Blog'],
['url' => 'https://blog.rust-lang.org/feed.xml', 'category' => 'programming_langs', 'name' => 'Rust Blog'],
['url' => 'https://blogs.python.org/feed/', 'category' => 'programming_langs', 'name' => 'Python Blog'],
['url' => 'https://jakearchibald.com/posts.rss', 'category' => 'programming_langs', 'name' => 'JavaScript Blog'],
['url' => 'https://blog.jetbrains.com/kotlin/feed/', 'category' => 'programming_langs', 'name' => 'Kotlin Blog'],

// Web Development
['url' => 'https://css-tricks.com/feed/', 'category' => 'web_dev', 'name' => 'CSS Tricks'],
['url' => 'https://www.smashingmagazine.com/feed/', 'category' => 'web_dev', 'name' => 'Smashing Magazine'],
['url' => 'https://dev.to/feed/', 'category' => 'web_dev', 'name' => 'DEV Community'],
['url' => 'https://web.dev/feed.xml', 'category' => 'web_dev', 'name' => 'Web.dev'],
['url' => 'https://stackoverflow.blog/feed/', 'category' => 'web_dev', 'name' => 'Stack Overflow Blog'],

// Software Architecture
['url' => 'https://martinfowler.com/feed.atom', 'category' => 'software_arch', 'name' => 'Martin Fowler'],
['url' => 'https://www.enterpriseintegrationpatterns.com/rss/eip.rss', 'category' => 'software_arch', 'name' => 'Enterprise Integration'],
['url' => 'https://microservices.io/feed.xml', 'category' => 'software_arch', 'name' => 'Microservices.io'],
['url' => 'https://blog.cleancoder.com/feed.xml', 'category' => 'software_arch', 'name' => 'Clean Coder'],

// DevOps
['url' => 'https://devops.com/feed/', 'category' => 'devops', 'name' => 'DevOps.com'],
['url' => 'https://kubernetes.io/feed.xml', 'category' => 'devops', 'name' => 'Kubernetes Blog'],
['url' => 'https://www.docker.com/blog/feed/', 'category' => 'devops', 'name' => 'Docker Blog'],
['url' => 'https://aws.amazon.com/blogs/devops/feed/', 'category' => 'devops', 'name' => 'AWS DevOps'],
['url' => 'https://cloud.google.com/blog/products/devops-sre/rss', 'category' => 'devops', 'name' => 'Google Cloud DevOps'],

// Computer Networks
['url' => 'https://blog.apnic.net/feed/', 'category' => 'networking', 'name' => 'APNIC Blog'],
['url' => 'https://blog.cloudflare.com/rss/', 'category' => 'networking', 'name' => 'Cloudflare Blog'],
['url' => 'https://www.cisco.com/c/en/us/about/blogs/networking-feed.xml', 'category' => 'networking', 'name' => 'Cisco Networking'],
['url' => 'https://ripe.net/rss/feed.xml', 'category' => 'networking', 'name' => 'RIPE Network'],

// Operating Systems
['url' => 'https://lwn.net/headlines/rss', 'category' => 'operating_systems', 'name' => 'Linux Weekly News'],
['url' => 'https://www.phoronix.com/rss.php', 'category' => 'operating_systems', 'name' => 'Phoronix'],
['url' => 'https://www.freebsd.org/news/feed.xml', 'category' => 'operating_systems', 'name' => 'FreeBSD News'],
['url' => 'https://blog.linuxmint.com/?feed=rss2', 'category' => 'operating_systems', 'name' => 'Linux Mint Blog'],

// Information Security
['url' => 'https://www.infosecurity-magazine.com/rss/news/', 'category' => 'infosec', 'name' => 'Infosecurity Magazine'],
['url' => 'https://www.helpnetsecurity.com/feed/', 'category' => 'infosec', 'name' => 'Help Net Security'],
['url' => 'https://www.securityfocus.com/rss/news.xml', 'category' => 'infosec', 'name' => 'Security Focus'],
['url' => 'https://www.csoonline.com/index.rss', 'category' => 'infosec', 'name' => 'CSO Online'],

// Computer Hardware
['url' => 'https://www.anandtech.com/rss/', 'category' => 'hardware', 'name' => 'AnandTech'],
['url' => 'https://www.tomshardware.com/feeds/all', 'category' => 'hardware', 'name' => 'Tom\'s Hardware'],
['url' => 'https://www.techpowerup.com/rss/', 'category' => 'hardware', 'name' => 'TechPowerUp'],
['url' => 'https://www.servethehome.com/feed/', 'category' => 'hardware', 'name' => 'ServeTheHome'],

// Quantum Computing
['url' => 'https://quantum-computing.ibm.com/feed/', 'category' => 'quantum_computing', 'name' => 'IBM Quantum'],
['url' => 'https://quantumcomputingreport.com/feed/', 'category' => 'quantum_computing', 'name' => 'Quantum Computing Report'],
['url' => 'https://www.dwavesys.com/feed/', 'category' => 'quantum_computing', 'name' => 'D-Wave Systems'],
['url' => 'https://ionq.com/feed', 'category' => 'quantum_computing', 'name' => 'IonQ News'],

// Privacy & Anonymity
['url' => 'https://www.privateinternetaccess.com/blog/feed/', 'category' => 'privacy', 'name' => 'Private Internet Access'],
['url' => 'https://www.torproject.org/feed/blog/', 'category' => 'privacy', 'name' => 'Tor Project'],
['url' => 'https://www.eff.org/rss/updates.xml', 'category' => 'privacy', 'name' => 'EFF Updates'],
['url' => 'https://www.epic.org/feed/', 'category' => 'privacy', 'name' => 'EPIC Privacy'],

// Distributed Systems
['url' => 'https://blog.acolyer.org/feed/', 'category' => 'distributed_systems', 'name' => 'The Morning Paper'],
['url' => 'https://www.allthingsdistributed.com/feed.xml', 'category' => 'distributed_systems', 'name' => 'All Things Distributed'],
['url' => 'https://muratbuffalo.blogspot.com/feeds/posts/default', 'category' => 'distributed_systems', 'name' => 'Distributed Systems Blog'],
['url' => 'https://martin.kleppmann.com/feed.xml', 'category' => 'distributed_systems', 'name' => 'Martin Kleppmann'],

// Compiler Design
['url' => 'https://blog.llvm.org/feed.xml', 'category' => 'compilers', 'name' => 'LLVM Blog'],
['url' => 'https://gcc.gnu.org/rss.xml', 'category' => 'compilers', 'name' => 'GCC News'],
['url' => 'https://devblogs.microsoft.com/cppblog/feed/', 'category' => 'compilers', 'name' => 'C++ Team Blog'],
['url' => 'https://blog.rust-lang.org/inside-rust/feed.xml', 'category' => 'compilers', 'name' => 'Inside Rust'],

// Internet Standards
['url' => 'https://www.ietf.org/blog/feed/', 'category' => 'internet_standards', 'name' => 'IETF Blog'],
['url' => 'https://www.w3.org/blog/feed/', 'category' => 'internet_standards', 'name' => 'W3C Blog'],
['url' => 'https://www.icann.org/en/feed/blogs', 'category' => 'internet_standards', 'name' => 'ICANN Blog'],
['url' => 'https://www.internetsociety.org/feed/', 'category' => 'internet_standards', 'name' => 'Internet Society'],
// Wildlife Conservation
['url' => 'https://www.worldwildlife.org/rss/feed.xml', 'category' => 'wildlife_conservation', 'name' => 'WWF News'],
['url' => 'https://www.iucn.org/news/feed', 'category' => 'wildlife_conservation', 'name' => 'IUCN News'],
['url' => 'https://www.fauna-flora.org/feed', 'category' => 'wildlife_conservation', 'name' => 'Fauna & Flora'],
['url' => 'https://www.panthera.org/feed', 'category' => 'wildlife_conservation', 'name' => 'Panthera'],
['url' => 'https://wildaid.org/feed/', 'category' => 'wildlife_conservation', 'name' => 'WildAid'],

// Bird Watching
['url' => 'https://www.audubon.org/rss.xml', 'category' => 'birding', 'name' => 'Audubon Society'],
['url' => 'https://www.birdlife.org/news/feed/', 'category' => 'birding', 'name' => 'BirdLife International'],
['url' => 'https://www.allaboutbirds.org/news/feed/', 'category' => 'birding', 'name' => 'All About Birds'],
['url' => 'https://www.rspb.org.uk/feed/news', 'category' => 'birding', 'name' => 'RSPB'],

// Marine Life
['url' => 'https://www.oceanconservancy.org/feed/', 'category' => 'marine_life', 'name' => 'Ocean Conservancy'],
['url' => 'https://www.marineconservation.org/feed/', 'category' => 'marine_life', 'name' => 'Marine Conservation'],
['url' => 'https://oceana.org/feed/', 'category' => 'marine_life', 'name' => 'Oceana'],
['url' => 'https://www.coralreef.gov/feed/', 'category' => 'marine_life', 'name' => 'Coral Reef News'],

// Rainforest Conservation
['url' => 'https://rainforestfoundation.org/feed/', 'category' => 'rainforest', 'name' => 'Rainforest Foundation'],
['url' => 'https://www.rainforest-alliance.org/feed/', 'category' => 'rainforest', 'name' => 'Rainforest Alliance'],
['url' => 'https://www.rainforesttrust.org/feed/', 'category' => 'rainforest', 'name' => 'Rainforest Trust'],
['url' => 'https://www.amazonconservation.org/feed/', 'category' => 'rainforest', 'name' => 'Amazon Conservation'],

// Endangered Species
['url' => 'https://www.endangeredspeciesinternational.org/feed/', 'category' => 'endangered_species', 'name' => 'ESI News'],
['url' => 'https://www.speciessurvival.org/feed/', 'category' => 'endangered_species', 'name' => 'Species Survival'],
['url' => 'https://www.traffic.org/feed/', 'category' => 'endangered_species', 'name' => 'TRAFFIC'],
['url' => 'https://www.edgeofexistence.org/feed/', 'category' => 'endangered_species', 'name' => 'EDGE Species'],

// Plant Life
['url' => 'https://www.bgci.org/feed/', 'category' => 'plant_life', 'name' => 'Botanic Gardens Conservation'],
['url' => 'https://www.plantlife.org.uk/feed', 'category' => 'plant_life', 'name' => 'Plantlife'],
['url' => 'https://www.kew.org/feeds/science', 'category' => 'plant_life', 'name' => 'Kew Science'],
['url' => 'https://www.nativeplanttrust.org/feed/', 'category' => 'plant_life', 'name' => 'Native Plant Trust'],

// Natural Parks
['url' => 'https://www.nps.gov/feeds/articles.htm', 'category' => 'natural_parks', 'name' => 'National Park Service'],
['url' => 'https://www.parkscanada.gc.ca/en/feed', 'category' => 'natural_parks', 'name' => 'Parks Canada'],
['url' => 'https://www.nationalparks.uk/feed/', 'category' => 'natural_parks', 'name' => 'UK National Parks'],
['url' => 'https://www.parkstrust.org/feed/', 'category' => 'natural_parks', 'name' => 'Parks Trust'],

// Desert Ecosystems
['url' => 'https://www.desertmuseum.org/feed/', 'category' => 'desert', 'name' => 'Desert Museum'],
['url' => 'https://www.desertecology.net/feed/', 'category' => 'desert', 'name' => 'Desert Ecology'],
['url' => 'https://www.deseretbiome.org/feed/', 'category' => 'desert', 'name' => 'Desert Biome'],
['url' => 'https://www.saharaconservation.org/feed/', 'category' => 'desert', 'name' => 'Sahara Conservation'],

// Arctic & Antarctic
['url' => 'https://www.antarcticanz.govt.nz/feed', 'category' => 'polar', 'name' => 'Antarctic News'],
['url' => 'https://www.bas.ac.uk/feed/', 'category' => 'polar', 'name' => 'British Antarctic Survey'],
['url' => 'https://www.arcticwwf.org/feed/', 'category' => 'polar', 'name' => 'Arctic WWF'],
['url' => 'https://www.polarresearch.net/index.php/polar/gateway/plugin/WebFeedGatewayPlugin/rss2', 'category' => 'polar', 'name' => 'Polar Research'],

// Insect Life
['url' => 'https://www.butterfliesandmoths.org/rss.xml', 'category' => 'insects', 'name' => 'Butterflies and Moths'],
['url' => 'https://www.saveourmonarchs.org/feed/', 'category' => 'insects', 'name' => 'Save Our Monarchs'],
['url' => 'https://www.buglife.org.uk/feed/', 'category' => 'insects', 'name' => 'Buglife'],
['url' => 'https://www.xerces.org/feed/', 'category' => 'insects', 'name' => 'Xerces Society'],

// Wetlands
['url' => 'https://www.wetlands.org/feed/', 'category' => 'wetlands', 'name' => 'Wetlands International'],
['url' => 'https://www.ramsar.org/feed/news', 'category' => 'wetlands', 'name' => 'Ramsar Convention'],
['url' => 'https://www.wetlandscience.org/feed/', 'category' => 'wetlands', 'name' => 'Wetland Science'],
['url' => 'https://www.ducks.org/feed/', 'category' => 'wetlands', 'name' => 'Ducks Unlimited'],

// Primate Conservation
['url' => 'https://www.janegoodall.org/feed/', 'category' => 'primates', 'name' => 'Jane Goodall Institute'],
['url' => 'https://www.orangutan.org.uk/feed/', 'category' => 'primates', 'name' => 'Orangutan Foundation'],
['url' => 'https://www.gorillas.org/feed/', 'category' => 'primates', 'name' => 'Dian Fossey Fund'],
['url' => 'https://www.lemur.org/feed/', 'category' => 'primates', 'name' => 'Lemur Conservation'],

// Nature Photography
['url' => 'https://www.naturephotographers.net/feed/', 'category' => 'nature_photography', 'name' => 'Nature Photographers'],
['url' => 'https://www.outdoorphotographer.com/feed/', 'category' => 'nature_photography', 'name' => 'Outdoor Photographer'],
['url' => 'https://www.naturettl.com/feed/', 'category' => 'nature_photography', 'name' => 'Nature TTL'],
['url' => 'https://www.wildplanetphoto.com/feed/', 'category' => 'nature_photography', 'name' => 'Wild Planet'],

// Reptiles & Amphibians
['url' => 'https://www.reptilesmagazine.com/feed/', 'category' => 'herps', 'name' => 'Reptiles Magazine'],
['url' => 'https://www.amphibians.org/feed/', 'category' => 'herps', 'name' => 'Amphibian Foundation'],
['url' => 'https://www.tortoisetrust.org/feed/', 'category' => 'herps', 'name' => 'Tortoise Trust'],
['url' => 'https://www.frogsafe.org.au/feed/', 'category' => 'herps', 'name' => 'Frog Safe'],

// Natural History Museums
['url' => 'https://www.nhm.ac.uk/rss/all-news.xml', 'category' => 'natural_history', 'name' => 'Natural History Museum London'],
['url' => 'https://www.amnh.org/feed', 'category' => 'natural_history', 'name' => 'American Museum of Natural History'],
['url' => 'https://naturalhistory.si.edu/feed', 'category' => 'natural_history', 'name' => 'Smithsonian Natural History'],
['url' => 'https://australian.museum/feed/', 'category' => 'natural_history', 'name' => 'Australian Museum']
    
    
  
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