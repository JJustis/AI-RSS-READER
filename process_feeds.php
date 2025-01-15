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
['url' => 'https://australian.museum/feed/', 'category' => 'natural_history', 'name' => 'Australian Museum'],
// Theoretical Physics
['url' => 'https://phys.org/physics-news/feed/', 'category' => 'theoretical_physics', 'name' => 'Physics.org'],
['url' => 'https://www.symmetrymagazine.org/feed', 'category' => 'theoretical_physics', 'name' => 'Symmetry Magazine'],
['url' => 'https://physics.aps.org/feed', 'category' => 'theoretical_physics', 'name' => 'Physics Magazine'],
['url' => 'https://www.quantamagazine.org/feed/physics/', 'category' => 'theoretical_physics', 'name' => 'Quanta Physics'],

// Biochemistry
['url' => 'https://www.biochemistry.org/feed/', 'category' => 'biochemistry', 'name' => 'Biochemical Society'],
['url' => 'https://pubs.acs.org/feed/biochem/rss', 'category' => 'biochemistry', 'name' => 'ACS Biochemistry'],
['url' => 'https://www.nature.com/nchembio.rss', 'category' => 'biochemistry', 'name' => 'Nature Chemical Biology'],
['url' => 'https://www.cellpress.com/action/showFeed?type=etoc&feed=rss&jc=jbc', 'category' => 'biochemistry', 'name' => 'Journal of Biological Chemistry'],

// Cosmology
['url' => 'https://www.universetoday.com/cosmology/feed/', 'category' => 'cosmology', 'name' => 'Universe Today Cosmology'],
['url' => 'https://www.skyandtelescope.com/astronomy-news/feed/', 'category' => 'cosmology', 'name' => 'Sky & Telescope'],
['url' => 'https://www.cosmologytoday.com/feed/', 'category' => 'cosmology', 'name' => 'Cosmology Today'],
['url' => 'https://www.space.com/cosmology/feed', 'category' => 'cosmology', 'name' => 'Space.com Cosmology'],

// Classical Literature
['url' => 'https://www.gutenberg.org/feed/', 'category' => 'classical_literature', 'name' => 'Project Gutenberg'],
['url' => 'https://publicdomainreview.org/feed/', 'category' => 'classical_literature', 'name' => 'Public Domain Review'],
['url' => 'https://www.classicalliterature.co.uk/feed/', 'category' => 'classical_literature', 'name' => 'Classical Literature'],
['url' => 'https://www.bl.uk/rss/collection-items/english-literature', 'category' => 'classical_literature', 'name' => 'British Library Literature'],

// Poetry and Verse
['url' => 'https://www.poetryfoundation.org/feed/poems', 'category' => 'poetry', 'name' => 'Poetry Foundation'],
['url' => 'https://poets.org/feed', 'category' => 'poetry', 'name' => 'Academy of American Poets'],
['url' => 'https://www.versedaily.org/feed', 'category' => 'poetry', 'name' => 'Verse Daily'],
['url' => 'https://www.poetryinternational.org/feed', 'category' => 'poetry', 'name' => 'Poetry International'],

// Modern Literature
['url' => 'https://lithub.com/feed/', 'category' => 'modern_literature', 'name' => 'Literary Hub'],
['url' => 'https://www.newyorker.com/feed/books', 'category' => 'modern_literature', 'name' => 'New Yorker Books'],
['url' => 'https://www.theparisreview.org/feed/', 'category' => 'modern_literature', 'name' => 'Paris Review'],
['url' => 'https://granta.com/feed/', 'category' => 'modern_literature', 'name' => 'Granta'],

// Science Fiction Literature
['url' => 'https://www.tor.com/feed', 'category' => 'sci_fi_lit', 'name' => 'Tor.com'],
['url' => 'https://locusmag.com/feed/', 'category' => 'sci_fi_lit', 'name' => 'Locus Magazine'],
['url' => 'https://www.sfwa.org/feed/', 'category' => 'sci_fi_lit', 'name' => 'SFWA'],
['url' => 'https://clarkesworldmagazine.com/feed/', 'category' => 'sci_fi_lit', 'name' => 'Clarkesworld'],

// Microbiology
['url' => 'https://www.microbiologyresearch.org/rss/content', 'category' => 'microbiology', 'name' => 'Microbiology Research'],
['url' => 'https://www.nature.com/nrmicro.rss', 'category' => 'microbiology', 'name' => 'Nature Microbiology'],
['url' => 'https://asm.org/rss/news', 'category' => 'microbiology', 'name' => 'ASM News'],
['url' => 'https://www.microbiologytoday.org/feed', 'category' => 'microbiology', 'name' => 'Microbiology Today'],

// Climate Science
['url' => 'https://climate.nasa.gov/feed/', 'category' => 'climate_science', 'name' => 'NASA Climate'],
['url' => 'https://www.carbonbrief.org/feed', 'category' => 'climate_science', 'name' => 'Carbon Brief'],
['url' => 'https://www.realclimate.org/feed/', 'category' => 'climate_science', 'name' => 'RealClimate'],
['url' => 'https://www.climatecentral.org/feed', 'category' => 'climate_science', 'name' => 'Climate Central'],

// Literary Criticism
['url' => 'https://www.lrb.co.uk/feeds/rss', 'category' => 'literary_criticism', 'name' => 'London Review of Books'],
['url' => 'https://www.nybooks.com/feed/', 'category' => 'literary_criticism', 'name' => 'NY Review of Books'],
['url' => 'https://www.bookforum.com/rss.xml', 'category' => 'literary_criticism', 'name' => 'Bookforum'],
['url' => 'https://www.thetls.co.uk/feed', 'category' => 'literary_criticism', 'name' => 'The TLS'],

// Fantasy Literature
['url' => 'https://fantasy-faction.com/feed', 'category' => 'fantasy_lit', 'name' => 'Fantasy Faction'],
['url' => 'https://www.fantasybookreview.co.uk/feed/', 'category' => 'fantasy_lit', 'name' => 'Fantasy Book Review'],
['url' => 'https://fantasy-magazine.com/feed', 'category' => 'fantasy_lit', 'name' => 'Fantasy Magazine'],
['url' => 'https://www.beneath-ceaseless-skies.com/feed/', 'category' => 'fantasy_lit', 'name' => 'Beneath Ceaseless Skies'],

// Geophysics
['url' => 'https://eos.org/feed', 'category' => 'geophysics', 'name' => 'EOS Earth & Space Science'],
['url' => 'https://agupubs.onlinelibrary.wiley.com/feed/19448007/most-recent', 'category' => 'geophysics', 'name' => 'Geophysical Research Letters'],
['url' => 'https://www.seismosoc.org/feed/', 'category' => 'geophysics', 'name' => 'Seismological Society'],
['url' => 'https://www.iris.edu/hq/feed', 'category' => 'geophysics', 'name' => 'IRIS Seismic Monitor'],

// Literary Awards
['url' => 'https://www.nobelprize.org/literature/feed/', 'category' => 'literary_awards', 'name' => 'Nobel Literature'],
['url' => 'https://themanbookerprize.com/rss', 'category' => 'literary_awards', 'name' => 'Booker Prize'],
['url' => 'https://www.pulitzer.org/feeds/prize_winners', 'category' => 'literary_awards', 'name' => 'Pulitzer Prizes'],
['url' => 'https://pen.org/feed', 'category' => 'literary_awards', 'name' => 'PEN America'],

// Immunology
['url' => 'https://www.nature.com/ni.rss', 'category' => 'immunology', 'name' => 'Nature Immunology'],
['url' => 'https://www.immunology.org/feed', 'category' => 'immunology', 'name' => 'British Society for Immunology'],
['url' => 'https://www.frontiersin.org/journals/immunology/feed', 'category' => 'immunology', 'name' => 'Frontiers in Immunology'],
['url' => 'https://www.jimmunol.org/rss/current.xml', 'category' => 'immunology', 'name' => 'Journal of Immunology'],

// World Literature
['url' => 'https://www.wordswithoutborders.org/rss', 'category' => 'world_literature', 'name' => 'Words Without Borders'],
['url' => 'https://www.asymptotejournal.com/feed', 'category' => 'world_literature', 'name' => 'Asymptote Journal'],
['url' => 'https://www.worldliteraturetoday.org/feed', 'category' => 'world_literature', 'name' => 'World Literature Today'],
['url' => 'https://www.bookbrainz.org/feed', 'category' => 'world_literature', 'name' => 'BookBrainz'],

// Mathematics
['url' => 'https://www.ams.org/rss/news', 'category' => 'mathematics', 'name' => 'American Mathematical Society'],
['url' => 'https://mathoverflow.net/feed', 'category' => 'mathematics', 'name' => 'MathOverflow'],
['url' => 'https://www.maa.org/feed', 'category' => 'mathematics', 'name' => 'Mathematical Association'],
['url' => 'https://plus.maths.org/content/feed', 'category' => 'mathematics', 'name' => 'Plus Magazine'],
// Organic Chemistry
['url' => 'https://pubs.acs.org/journal/joceah/rss', 'category' => 'organic_chem', 'name' => 'Journal of Organic Chemistry'],
['url' => 'https://onlinelibrary.wiley.com/feed/10990690/most-recent', 'category' => 'organic_chem', 'name' => 'European Journal of Organic Chemistry'],
['url' => 'https://www.organic-chemistry.org/feed/', 'category' => 'organic_chem', 'name' => 'Organic Chemistry Portal'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/ob?issueid=recent#!recentarticles', 'category' => 'organic_chem', 'name' => 'Organic & Biomolecular Chemistry'],

// Inorganic Chemistry
['url' => 'https://pubs.acs.org/journal/inocaj/rss', 'category' => 'inorganic_chem', 'name' => 'Inorganic Chemistry'],
['url' => 'https://www.sciencedirect.com/journal/inorganica-chimica-acta/rss', 'category' => 'inorganic_chem', 'name' => 'Inorganica Chimica Acta'],
['url' => 'https://onlinelibrary.wiley.com/feed/15213773/most-recent', 'category' => 'inorganic_chem', 'name' => 'European Journal of Inorganic Chemistry'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/dt?issueid=recent#!recentarticles', 'category' => 'inorganic_chem', 'name' => 'Dalton Transactions'],

// Physical Chemistry
['url' => 'https://pubs.acs.org/journal/jpcafh/rss', 'category' => 'physical_chem', 'name' => 'Journal of Physical Chemistry'],
['url' => 'https://www.nature.com/subjects/physical-chemistry.rss', 'category' => 'physical_chem', 'name' => 'Nature Physical Chemistry'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/cp?issueid=recent#!recentarticles', 'category' => 'physical_chem', 'name' => 'Physical Chemistry Chemical Physics'],
['url' => 'https://onlinelibrary.wiley.com/feed/16164164/most-recent', 'category' => 'physical_chem', 'name' => 'ChemPhysChem'],

// Analytical Chemistry
['url' => 'https://pubs.acs.org/journal/ancham/rss', 'category' => 'analytical_chem', 'name' => 'Analytical Chemistry'],
['url' => 'https://www.sciencedirect.com/journal/analytica-chimica-acta/rss', 'category' => 'analytical_chem', 'name' => 'Analytica Chimica Acta'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/an?issueid=recent#!recentarticles', 'category' => 'analytical_chem', 'name' => 'Analyst'],
['url' => 'https://onlinelibrary.wiley.com/feed/10991565/most-recent', 'category' => 'analytical_chem', 'name' => 'Journal of Analytical Science'],

// Biochemistry
['url' => 'https://pubs.acs.org/journal/bichaw/rss', 'category' => 'biochemistry', 'name' => 'Biochemistry'],
['url' => 'https://www.jbc.org/rss/recent.xml', 'category' => 'biochemistry', 'name' => 'Journal of Biological Chemistry'],
['url' => 'https://febs.onlinelibrary.wiley.com/feed/18733468/most-recent', 'category' => 'biochemistry', 'name' => 'FEBS Journal'],
['url' => 'https://www.nature.com/subjects/biochemistry.rss', 'category' => 'biochemistry', 'name' => 'Nature Biochemistry'],

// Polymer Chemistry
['url' => 'https://pubs.acs.org/journal/mamobx/rss', 'category' => 'polymer_chem', 'name' => 'Macromolecules'],
['url' => 'https://onlinelibrary.wiley.com/feed/15213935/most-recent', 'category' => 'polymer_chem', 'name' => 'Macromolecular Chemistry and Physics'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/py?issueid=recent#!recentarticles', 'category' => 'polymer_chem', 'name' => 'Polymer Chemistry'],
['url' => 'https://www.sciencedirect.com/journal/polymer/rss', 'category' => 'polymer_chem', 'name' => 'Polymer Journal'],

// Computational Chemistry
['url' => 'https://pubs.acs.org/journal/jctcce/rss', 'category' => 'comp_chem', 'name' => 'Journal of Chemical Theory and Computation'],
['url' => 'https://onlinelibrary.wiley.com/feed/1096987X/most-recent', 'category' => 'comp_chem', 'name' => 'Journal of Computational Chemistry'],
['url' => 'https://www.sciencedirect.com/journal/computational-and-theoretical-chemistry/rss', 'category' => 'comp_chem', 'name' => 'Computational and Theoretical Chemistry'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/me?issueid=recent#!recentarticles', 'category' => 'comp_chem', 'name' => 'Molecular Systems Design & Engineering'],

// Medicinal Chemistry
['url' => 'https://pubs.acs.org/journal/jmcmar/rss', 'category' => 'medicinal_chem', 'name' => 'Journal of Medicinal Chemistry'],
['url' => 'https://www.nature.com/subjects/medicinal-chemistry.rss', 'category' => 'medicinal_chem', 'name' => 'Nature Medicinal Chemistry'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/md?issueid=recent#!recentarticles', 'category' => 'medicinal_chem', 'name' => 'MedChemComm'],
['url' => 'https://www.sciencedirect.com/journal/bioorganic-and-medicinal-chemistry/rss', 'category' => 'medicinal_chem', 'name' => 'Bioorganic & Medicinal Chemistry'],

// Environmental Chemistry
['url' => 'https://pubs.acs.org/journal/esthag/rss', 'category' => 'environmental_chem', 'name' => 'Environmental Science & Technology'],
['url' => 'https://www.nature.com/subjects/environmental-chemistry.rss', 'category' => 'environmental_chem', 'name' => 'Nature Environmental Chemistry'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/em?issueid=recent#!recentarticles', 'category' => 'environmental_chem', 'name' => 'Environmental Science: Processes & Impacts'],
['url' => 'https://www.sciencedirect.com/journal/chemosphere/rss', 'category' => 'environmental_chem', 'name' => 'Chemosphere'],

// Materials Chemistry
['url' => 'https://pubs.acs.org/journal/ancac3/rss', 'category' => 'materials_chem', 'name' => 'ACS Nano'],
['url' => 'https://www.nature.com/nmat.rss', 'category' => 'materials_chem', 'name' => 'Nature Materials'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/ta?issueid=recent#!recentarticles', 'category' => 'materials_chem', 'name' => 'Journal of Materials Chemistry A'],
['url' => 'https://onlinelibrary.wiley.com/feed/15214095/most-recent', 'category' => 'materials_chem', 'name' => 'Advanced Materials'],

// Crystallography
['url' => 'https://journals.iucr.org/feeds/rss.xml', 'category' => 'crystallography', 'name' => 'IUCr Journals'],
['url' => 'https://www.crystallography.net/feed/', 'category' => 'crystallography', 'name' => 'Crystallography Open Database'],
['url' => 'https://scripts.iucr.org/rss/recent.xml', 'category' => 'crystallography', 'name' => 'IUCr Recent Papers'],
['url' => 'https://www.iucr.org/news/feed', 'category' => 'crystallography', 'name' => 'IUCr News'],

// Electrochemistry
['url' => 'https://pubs.acs.org/journal/jpcafh/rss', 'category' => 'electrochemistry', 'name' => 'Journal of Physical Chemistry'],
['url' => 'https://www.sciencedirect.com/journal/electrochemistry-communications/rss', 'category' => 'electrochemistry', 'name' => 'Electrochemistry Communications'],
['url' => 'https://pubs.rsc.org/en/journals/journalissues/cp?issueid=recent#!recentarticles', 'category' => 'electrochemistry', 'name' => 'Physical Chemistry Chemical Physics'],
['url' => 'https://onlinelibrary.wiley.com/feed/15214095/most-recent', 'category' => 'electrochemistry', 'name' => 'ChemElectroChem'],
// Add these to your existing getKnownFeeds() array

// Algorithms & Data Structures
['url' => 'https://algorithms.openmindmap.org/feed/', 'category' => 'algorithms', 'name' => 'Algorithm Watch'],
['url' => 'https://theory.stanford.edu/main/feed/', 'category' => 'algorithms', 'name' => 'Stanford Theory'],
['url' => 'https://algorithmsoup.wordpress.com/feed/', 'category' => 'algorithms', 'name' => 'Algorithm Soup'],
['url' => 'https://algorithmist.com/feed/', 'category' => 'algorithms', 'name' => 'The Algorithmist'],

// Machine Learning Research
['url' => 'https://machinelearning.apple.com/rss.xml', 'category' => 'ml_research', 'name' => 'Apple Machine Learning'],
['url' => 'https://ai.googleblog.com/feeds/posts/default', 'category' => 'ml_research', 'name' => 'Google AI Blog'],
['url' => 'https://research.facebook.com/blog/rss/', 'category' => 'ml_research', 'name' => 'Meta AI Research'],
['url' => 'https://openai.com/blog/rss/', 'category' => 'ml_research', 'name' => 'OpenAI Blog'],

// Computer Architecture
['url' => 'https://www.computer.org/digital-library/rss/magazines/mi', 'category' => 'comp_arch', 'name' => 'IEEE Micro'],
['url' => 'https://cacm.acm.org/blogs/blog-cacm.rss', 'category' => 'comp_arch', 'name' => 'CACM Blog'],
['url' => 'https://www.anandtech.com/rss/', 'category' => 'comp_arch', 'name' => 'AnandTech'],
['url' => 'https://www.servethehome.com/feed/', 'category' => 'comp_arch', 'name' => 'ServeTheHome'],

// Software Engineering
['url' => 'https://martinfowler.com/feed.atom', 'category' => 'software_eng', 'name' => 'Martin Fowler'],
['url' => 'https://blog.codinghorror.com/rss/', 'category' => 'software_eng', 'name' => 'Coding Horror'],
['url' => 'https://www.joelonsoftware.com/feed/', 'category' => 'software_eng', 'name' => 'Joel on Software'],
['url' => 'https://blog.cleancoder.com/feed.xml', 'category' => 'software_eng', 'name' => 'Clean Coder'],

// Computer Graphics
['url' => 'https://www.siggraph.org/feed/', 'category' => 'graphics', 'name' => 'SIGGRAPH News'],
['url' => 'https://blogs.nvidia.com/feed/', 'category' => 'graphics', 'name' => 'NVIDIA Blog'],
['url' => 'https://www.khronos.org/feed', 'category' => 'graphics', 'name' => 'Khronos Group'],
['url' => 'https://www.renderingeye.com/feed/', 'category' => 'graphics', 'name' => 'Rendering Eye'],

// Database Systems
['url' => 'https://planet.postgresql.org/rss20.xml', 'category' => 'databases', 'name' => 'PostgreSQL Planet'],
['url' => 'https://dev.mysql.com/blog-archive/rss/', 'category' => 'databases', 'name' => 'MySQL Blog'],
['url' => 'https://blog.mongodb.com/rss/', 'category' => 'databases', 'name' => 'MongoDB Blog'],
['url' => 'https://redis.com/blog/feed/', 'category' => 'databases', 'name' => 'Redis Blog'],

// Computer Networks
['url' => 'https://blog.cloudflare.com/rss/', 'category' => 'networking', 'name' => 'Cloudflare Blog'],
['url' => 'https://arstechnica.com/information-technology/feed/', 'category' => 'networking', 'name' => 'Ars Technica IT'],
['url' => 'https://www.networkworld.com/feed/', 'category' => 'networking', 'name' => 'Network World'],
['url' => 'https://blog.apnic.net/feed/', 'category' => 'networking', 'name' => 'APNIC Blog'],

// Quantum Computing News
['url' => 'https://www.ibm.com/quantum/feed/', 'category' => 'quantum_computing', 'name' => 'IBM Quantum'],
['url' => 'https://quantumxc.com/feed/', 'category' => 'quantum_computing', 'name' => 'QuantumXC'],
['url' => 'https://quantum-journal.org/feed/', 'category' => 'quantum_computing', 'name' => 'Quantum Journal'],
['url' => 'https://quantumcomputingreport.com/feed/', 'category' => 'quantum_computing', 'name' => 'Quantum Computing Report'],

// Quantum Physics News
['url' => 'https://phys.org/rss-feed/physics-news/quantum-physics/', 'category' => 'quantum_physics', 'name' => 'Phys.org Quantum'],
['url' => 'https://www.quantamagazine.org/feed/', 'category' => 'quantum_physics', 'name' => 'Quanta Magazine'],
['url' => 'https://physicsworld.com/category/quantum/feed/', 'category' => 'quantum_physics', 'name' => 'Physics World Quantum'],
['url' => 'https://www.nature.com/subjects/quantum-physics.rss', 'category' => 'quantum_physics', 'name' => 'Nature Quantum Physics'],

// Quantum Technology
['url' => 'https://quantum.tech/feed/', 'category' => 'quantum_tech', 'name' => 'Quantum.Tech'],
['url' => 'https://thequantuminsider.com/feed/', 'category' => 'quantum_tech', 'name' => 'The Quantum Insider'],
['url' => 'https://www.dwavesys.com/feed/', 'category' => 'quantum_tech', 'name' => 'D-Wave Systems'],
['url' => 'https://ionq.com/blog/rss.xml', 'category' => 'quantum_tech', 'name' => 'IonQ Blog'],

// Theoretical Computer Science
['url' => 'https://theory.cs.princeton.edu/feed.xml', 'category' => 'theoretical_cs', 'name' => 'Princeton Theory'],
['url' => 'https://blog.computationalcomplexity.org/feeds/posts/default', 'category' => 'theoretical_cs', 'name' => 'Computational Complexity'],
['url' => 'https://cstheory.stackexchange.com/feeds', 'category' => 'theoretical_cs', 'name' => 'CS Theory Stack Exchange'],
['url' => 'https://theoryofcomputing.org/feed.rss', 'category' => 'theoretical_cs', 'name' => 'Theory of Computing'],

// Programming Languages Research
['url' => 'https://blog.sigplan.org/feed/', 'category' => 'pl_research', 'name' => 'SIGPLAN Blog'],
['url' => 'https://lambda-the-ultimate.org/rss.xml', 'category' => 'pl_research', 'name' => 'Lambda the Ultimate'],
['url' => 'https://www.pl-enthusiast.net/feed/', 'category' => 'pl_research', 'name' => 'Programming Languages Enthusiast'],
['url' => 'https://pldi.blogspot.com/feeds/posts/default', 'category' => 'pl_research', 'name' => 'PLDI Blog'],

// Computer Security Research
['url' => 'https://www.schneier.com/feed/atom/', 'category' => 'security_research', 'name' => 'Schneier on Security'],
['url' => 'https://googleprojectzero.blogspot.com/feeds/posts/default', 'category' => 'security_research', 'name' => 'Project Zero'],
['url' => 'https://www.imperialviolet.org/feed.xml', 'category' => 'security_research', 'name' => 'ImperialViolet'],
['url' => 'https://cyber.harvard.edu/rss.xml', 'category' => 'security_research', 'name' => 'Harvard Cybersecurity'],

// Quantum Cryptography
['url' => 'https://www.idquantique.com/feed/', 'category' => 'quantum_crypto', 'name' => 'ID Quantique'],
['url' => 'https://quantumxc.com/blog/feed/', 'category' => 'quantum_crypto', 'name' => 'Quantum Xchange'],
['url' => 'https://www.quintessencelabs.com/feed', 'category' => 'quantum_crypto', 'name' => 'QuintessenceLabs'],
['url' => 'https://www.toshiba.eu/feeds/quantum', 'category' => 'quantum_crypto', 'name' => 'Toshiba Quantum'],

// Human-Computer Interaction
['url' => 'https://interactions.acm.org/feed', 'category' => 'hci', 'name' => 'ACM Interactions'],
['url' => 'https://www.nngroup.com/feed/rss/', 'category' => 'hci', 'name' => 'Nielsen Norman Group'],
['url' => 'https://uxdesign.cc/feed', 'category' => 'hci', 'name' => 'UX Collective'],
['url' => 'https://www.interaction-design.org/feed', 'category' => 'hci', 'name' => 'Interaction Design Foundation'],
// Add these to your existing getKnownFeeds() array

// Astrobiology
['url' => 'https://www.astrobio.net/feed/', 'category' => 'astrobiology', 'name' => 'Astrobiology Magazine'],
['url' => 'https://nai.nasa.gov/rss/news/', 'category' => 'astrobiology', 'name' => 'NASA Astrobiology'],
['url' => 'https://www.seti.org/rss.xml', 'category' => 'astrobiology', 'name' => 'SETI Institute'],
['url' => 'https://exoplanets.nasa.gov/rss/news/', 'category' => 'astrobiology', 'name' => 'NASA Exoplanets'],

// Biophysics
['url' => 'https://www.biophysics.org/rss/news', 'category' => 'biophysics', 'name' => 'Biophysical Society'],
['url' => 'https://www.nature.com/subjects/biophysics.rss', 'category' => 'biophysics', 'name' => 'Nature Biophysics'],
['url' => 'https://www.cell.com/biophysj/rss', 'category' => 'biophysics', 'name' => 'Biophysical Journal'],
['url' => 'https://journals.aps.org/prx/feed', 'category' => 'biophysics', 'name' => 'Physical Review X'],

// Chronobiology
['url' => 'https://www.chronobiology.com/feed/', 'category' => 'chronobiology', 'name' => 'Chronobiology News'],
['url' => 'https://www.sciencedaily.com/rss/plants_animals/circadian_rhythms.xml', 'category' => 'chronobiology', 'name' => 'ScienceDaily Circadian'],
['url' => 'https://journals.sagepub.com/action/showFeed?type=etoc&feed=rss&jc=jbrb', 'category' => 'chronobiology', 'name' => 'Journal of Biological Rhythms'],
['url' => 'https://www.nature.com/subjects/circadian-rhythms.rss', 'category' => 'chronobiology', 'name' => 'Nature Circadian Rhythms'],

// Ethnobotany
['url' => 'https://www.botany.org/feed/', 'category' => 'ethnobotany', 'name' => 'Botanical Society of America'],
['url' => 'https://ethnobotanyjournal.org/feed/', 'category' => 'ethnobotany', 'name' => 'Economic Botany'],
['url' => 'https://www.kew.org/science/rss', 'category' => 'ethnobotany', 'name' => 'Kew Science'],
['url' => 'https://www.sciencedaily.com/rss/plants_animals/botany.xml', 'category' => 'ethnobotany', 'name' => 'ScienceDaily Botany'],

// Biomechanics
['url' => 'https://www.mechanobio.info/feed/', 'category' => 'biomechanics', 'name' => 'MechanoBio'],
['url' => 'https://www.sciencedaily.com/rss/matter_energy/mechanics.xml', 'category' => 'biomechanics', 'name' => 'ScienceDaily Mechanics'],
['url' => 'https://jeb.biologists.org/rss/current.xml', 'category' => 'biomechanics', 'name' => 'Journal of Experimental Biology'],
['url' => 'https://www.nature.com/subjects/biomechanics.rss', 'category' => 'biomechanics', 'name' => 'Nature Biomechanics'],

// Cryogenics
['url' => 'https://cryogenicsociety.org/feed/', 'category' => 'cryogenics', 'name' => 'Cryogenic Society'],
['url' => 'https://www.sciencedaily.com/rss/matter_energy/cryogenics.xml', 'category' => 'cryogenics', 'name' => 'ScienceDaily Cryogenics'],
['url' => 'https://www.nature.com/subjects/cryogenics.rss', 'category' => 'cryogenics', 'name' => 'Nature Cryogenics'],
['url' => 'https://www.cryogenicsinternational.com/feed/', 'category' => 'cryogenics', 'name' => 'Cryogenics International'],

// Game Theory
['url' => 'https://www.gametheory.net/feed/', 'category' => 'game_theory', 'name' => 'Game Theory .net'],
['url' => 'https://www.nature.com/subjects/game-theory.rss', 'category' => 'game_theory', 'name' => 'Nature Game Theory'],
['url' => 'https://www.sciencedaily.com/rss/computers_math/game_theory.xml', 'category' => 'game_theory', 'name' => 'ScienceDaily Game Theory'],
['url' => 'https://www.pnas.org/action/showFeed?type=etoc&feed=rss&jc=game', 'category' => 'game_theory', 'name' => 'PNAS Game Theory'],

// Bionics
['url' => 'https://www.bionicslab.com/feed/', 'category' => 'bionics', 'name' => 'Bionics Lab'],
['url' => 'https://www.sciencedaily.com/rss/matter_energy/bio-engineering.xml', 'category' => 'bionics', 'name' => 'ScienceDaily Bioengineering'],
['url' => 'https://www.nature.com/subjects/bionics.rss', 'category' => 'bionics', 'name' => 'Nature Bionics'],
['url' => 'https://www.ieee.org/rss/bionics', 'category' => 'bionics', 'name' => 'IEEE Bionics'],

// Archaeogenetics
['url' => 'https://www.sciencedaily.com/rss/fossils_ruins/ancient_dna.xml', 'category' => 'archaeogenetics', 'name' => 'ScienceDaily Ancient DNA'],
['url' => 'https://www.nature.com/subjects/archaeogenetics.rss', 'category' => 'archaeogenetics', 'name' => 'Nature Archaeogenetics'],
['url' => 'https://www.cell.com/trends/genetics/rss', 'category' => 'archaeogenetics', 'name' => 'Trends in Genetics'],
['url' => 'https://academic.oup.com/mbe/feed', 'category' => 'archaeogenetics', 'name' => 'Molecular Biology and Evolution'],

// Biomimicry
['url' => 'https://biomimicry.org/feed/', 'category' => 'biomimicry', 'name' => 'Biomimicry Institute'],
['url' => 'https://www.asknature.org/feed/', 'category' => 'biomimicry', 'name' => 'Ask Nature'],
['url' => 'https://www.sciencedaily.com/rss/matter_energy/nature_inspired_technology.xml', 'category' => 'biomimicry', 'name' => 'ScienceDaily Biomimicry'],
['url' => 'https://www.nature.com/subjects/biomimetics.rss', 'category' => 'biomimicry', 'name' => 'Nature Biomimetics'],

// Chaos Theory
['url' => 'https://www.chaosscience.org.uk/feed', 'category' => 'chaos_theory', 'name' => 'Chaos Science'],
['url' => 'https://www.sciencedaily.com/rss/computers_math/chaos_theory.xml', 'category' => 'chaos_theory', 'name' => 'ScienceDaily Chaos'],
['url' => 'https://www.nature.com/subjects/nonlinear-phenomena.rss', 'category' => 'chaos_theory', 'name' => 'Nature Nonlinear Dynamics'],
['url' => 'https://aip.scitation.org/feed/cha/most-recent', 'category' => 'chaos_theory', 'name' => 'Chaos Journal'],

// Systems Biology
['url' => 'https://www.systemsbiology.org/feed/', 'category' => 'systems_biology', 'name' => 'Institute for Systems Biology'],
['url' => 'https://www.cell.com/systems/rss', 'category' => 'systems_biology', 'name' => 'Cell Systems'],
['url' => 'https://www.nature.com/subjects/systems-biology.rss', 'category' => 'systems_biology', 'name' => 'Nature Systems Biology'],
['url' => 'https://bmcsystbiol.biomedcentral.com/articles/feed.rss', 'category' => 'systems_biology', 'name' => 'BMC Systems Biology'],

// Xenobiology
['url' => 'https://www.sciencedaily.com/rss/strange_science/synthetic_biology.xml', 'category' => 'xenobiology', 'name' => 'ScienceDaily Synthetic Bio'],
['url' => 'https://www.nature.com/subjects/synthetic-biology.rss', 'category' => 'xenobiology', 'name' => 'Nature Synthetic Biology'],
['url' => 'https://synbiobeta.com/feed/', 'category' => 'xenobiology', 'name' => 'SynBioBeta'],
['url' => 'https://pubs.acs.org/journal/asbcd6/rss', 'category' => 'xenobiology', 'name' => 'ACS Synthetic Biology'],

// Dendrochronology
['url' => 'https://www.sciencedaily.com/rss/earth_climate/trees.xml', 'category' => 'dendrochronology', 'name' => 'ScienceDaily Trees'],
['url' => 'https://www.nature.com/subjects/dendrochronology.rss', 'category' => 'dendrochronology', 'name' => 'Nature Dendrochronology'],
['url' => 'https://www.tree-ring.org/feed/', 'category' => 'dendrochronology', 'name' => 'Tree-Ring Society'],
['url' => 'https://www.dendrochronologia.org/feed/', 'category' => 'dendrochronology', 'name' => 'Dendrochronologia Journal'],

// Mathematical Biology
['url' => 'https://www.sciencedaily.com/rss/computers_math/mathematical_biology.xml', 'category' => 'math_biology', 'name' => 'ScienceDaily Math Bio'],
['url' => 'https://royalsocietypublishing.org/action/showFeed?type=etoc&feed=rss&jc=jrsi', 'category' => 'math_biology', 'name' => 'Royal Society Interface'],
['url' => 'https://academic.oup.com/imammb/feed', 'category' => 'math_biology', 'name' => 'Mathematical Medicine and Biology'],
['url' => 'https://www.nature.com/subjects/biomathematics.rss', 'category' => 'math_biology', 'name' => 'Nature Biomathematics'],
// Add these to your existing getKnownFeeds() array

// Transparency Organizations
['url' => 'https://www.transparency.org/news/rss', 'category' => 'transparency', 'name' => 'Transparency International'],
['url' => 'https://freedomhouse.org/rss.xml', 'category' => 'transparency', 'name' => 'Freedom House'],
['url' => 'https://sunlightfoundation.com/feed/', 'category' => 'transparency', 'name' => 'Sunlight Foundation'],
['url' => 'https://www.opensecrets.org/news/feed/', 'category' => 'transparency', 'name' => 'OpenSecrets'],

// Investigative Journalism
['url' => 'https://www.icij.org/feed/', 'category' => 'investigative', 'name' => 'International Consortium of Investigative Journalists'],
['url' => 'https://www.propublica.org/feed/main', 'category' => 'investigative', 'name' => 'ProPublica'],
['url' => 'https://www.bellingcat.com/feed/', 'category' => 'investigative', 'name' => 'Bellingcat'],
['url' => 'https://theintercept.com/feed/?rss', 'category' => 'investigative', 'name' => 'The Intercept'],

// Digital Rights
['url' => 'https://www.eff.org/rss/updates.xml', 'category' => 'digital_rights', 'name' => 'Electronic Frontier Foundation'],
['url' => 'https://www.accessnow.org/feed/', 'category' => 'digital_rights', 'name' => 'Access Now'],
['url' => 'https://www.privacyinternational.org/rss.xml', 'category' => 'digital_rights', 'name' => 'Privacy International'],
['url' => 'https://www.digitalrights.ie/feed/', 'category' => 'digital_rights', 'name' => 'Digital Rights Ireland'],

// Government Accountability
['url' => 'https://www.pogo.org/feed.xml', 'category' => 'govt_accountability', 'name' => 'Project On Government Oversight'],
['url' => 'https://www.gao.gov/rss/reports.rss', 'category' => 'govt_accountability', 'name' => 'US Government Accountability Office'],
['url' => 'https://www.taxpayer.net/feed/', 'category' => 'govt_accountability', 'name' => 'Taxpayers for Common Sense'],
['url' => 'https://www.whistleblower.org/feed/', 'category' => 'govt_accountability', 'name' => 'Government Accountability Project'],

// Human Rights Monitoring
['url' => 'https://www.hrw.org/rss.xml', 'category' => 'human_rights', 'name' => 'Human Rights Watch'],
['url' => 'https://www.amnesty.org/en/feed/', 'category' => 'human_rights', 'name' => 'Amnesty International'],
['url' => 'https://www.frontlinedefenders.org/en/rss.xml', 'category' => 'human_rights', 'name' => 'Front Line Defenders'],
['url' => 'https://www.article19.org/feed/', 'category' => 'human_rights', 'name' => 'ARTICLE 19'],

// Press Freedom
['url' => 'https://rsf.org/en/rss.xml', 'category' => 'press_freedom', 'name' => 'Reporters Without Borders'],
['url' => 'https://cpj.org/feed/', 'category' => 'press_freedom', 'name' => 'Committee to Protect Journalists'],
['url' => 'https://www.indexoncensorship.org/feed/', 'category' => 'press_freedom', 'name' => 'Index on Censorship'],
['url' => 'https://freedomofthepress.org/feed/', 'category' => 'press_freedom', 'name' => 'Freedom of the Press Foundation'],

// Data Journalism
['url' => 'https://datajournalism.com/feed', 'category' => 'data_journalism', 'name' => 'DataJournalism.com'],
['url' => 'https://www.thebureauinvestigates.com/feed.xml', 'category' => 'data_journalism', 'name' => 'The Bureau of Investigative Journalism'],
['url' => 'https://www.revealnews.org/feed/', 'category' => 'data_journalism', 'name' => 'Reveal News'],
['url' => 'https://fivethirtyeight.com/feed/', 'category' => 'data_journalism', 'name' => 'FiveThirtyEight'],

// Document Archives
['url' => 'https://www.documentcloud.org/rss/public.xml', 'category' => 'document_archives', 'name' => 'DocumentCloud'],
['url' => 'https://www.muckrock.com/news/feed/', 'category' => 'document_archives', 'name' => 'MuckRock'],
['url' => 'https://www.archives.gov/feed.xml', 'category' => 'document_archives', 'name' => 'National Archives'],
['url' => 'https://www.governmentattic.org/rss.xml', 'category' => 'document_archives', 'name' => 'GovernmentAttic'],

// Alternative Media
['url' => 'https://truthout.org/feed', 'category' => 'alternative_media', 'name' => 'Truthout'],
['url' => 'https://fair.org/feed/', 'category' => 'alternative_media', 'name' => 'Fairness & Accuracy In Reporting'],
['url' => 'https://www.democracynow.org/democracynow.rss', 'category' => 'alternative_media', 'name' => 'Democracy Now'],
['url' => 'https://www.motherjones.com/feed/', 'category' => 'alternative_media', 'name' => 'Mother Jones'],

// Security Research
['url' => 'https://www.schneier.com/feed/atom/', 'category' => 'security_research', 'name' => 'Schneier on Security'],
['url' => 'https://krebsonsecurity.com/feed/', 'category' => 'security_research', 'name' => 'Krebs on Security'],
['url' => 'https://www.darkreading.com/rss.xml', 'category' => 'security_research', 'name' => 'Dark Reading'],
['url' => 'https://www.securityweek.com/feed/', 'category' => 'security_research', 'name' => 'Security Week'],
// Add these to your existing getKnownFeeds() array

// Pokemon News
['url' => 'https://www.serebii.net/rss.xml', 'category' => 'pokemon', 'name' => 'Serebii.net'],
['url' => 'https://pokemonblog.com/feed/', 'category' => 'pokemon', 'name' => 'Pokemon Blog'],
['url' => 'https://www.pokemon.com/us/pokemon-news/rss', 'category' => 'pokemon', 'name' => 'Official Pokemon News'],
['url' => 'https://pokejungle.net/feed/', 'category' => 'pokemon', 'name' => 'PokeJungle'],

// Pokemon Competitive
['url' => 'https://www.smogon.com/rss/', 'category' => 'pokemon_competitive', 'name' => 'Smogon University'],
['url' => 'https://www.pokemonvgc.com/feed/', 'category' => 'pokemon_competitive', 'name' => 'Pokemon VGC'],
['url' => 'https://victoryroadvgc.com/feed/', 'category' => 'pokemon_competitive', 'name' => 'Victory Road'],
['url' => 'https://www.trainertower.com/feed/', 'category' => 'pokemon_competitive', 'name' => 'Trainer Tower'],

// Pokemon Trading Card Game
['url' => 'https://www.pokebeach.com/feed', 'category' => 'pokemon_tcg', 'name' => 'PokeBeach'],
['url' => 'https://www.pokellector.com/feed/', 'category' => 'pokemon_tcg', 'name' => 'Pokellector'],
['url' => 'https://pokegym.net/feed/', 'category' => 'pokemon_tcg', 'name' => 'The Pojo'],
['url' => 'https://www.pokeguardian.com/feed/', 'category' => 'pokemon_tcg', 'name' => 'PokeGuardian'],

// Nintendo News
['url' => 'https://www.nintendo.com/whatsnew/rss', 'category' => 'nintendo', 'name' => 'Official Nintendo News'],
['url' => 'https://nintendoeverything.com/feed/', 'category' => 'nintendo', 'name' => 'Nintendo Everything'],
['url' => 'https://mynintendonews.com/feed/', 'category' => 'nintendo', 'name' => 'My Nintendo News'],
['url' => 'https://nintendowire.com/feed/', 'category' => 'nintendo', 'name' => 'Nintendo Wire'],

// Zelda News
['url' => 'https://www.zeldadungeon.net/feed/', 'category' => 'zelda', 'name' => 'Zelda Dungeon'],
['url' => 'https://zelda.com/news/rss', 'category' => 'zelda', 'name' => 'Official Zelda News'],
['url' => 'https://www.zeldainformer.com/feed/', 'category' => 'zelda', 'name' => 'Zelda Informer'],
['url' => 'https://zeldauniverse.net/feed/', 'category' => 'zelda', 'name' => 'Zelda Universe'],

// Mario News
['url' => 'https://www.mariowiki.com/feed.php', 'category' => 'mario', 'name' => 'Mario Wiki'],
['url' => 'https://www.marioboards.com/feed/', 'category' => 'mario', 'name' => 'Mario Boards'],
['url' => 'https://www.mariouniverse.com/feed/', 'category' => 'mario', 'name' => 'Mario Universe'],
['url' => 'https://www.supermario.com/news/feed/', 'category' => 'mario', 'name' => 'Super Mario Official'],

// Anime News
['url' => 'https://www.animenewsnetwork.com/newsroom/rss.xml', 'category' => 'anime', 'name' => 'Anime News Network'],
['url' => 'https://www.crunchyroll.com/feed', 'category' => 'anime', 'name' => 'Crunchyroll News'],
['url' => 'https://otakumode.com/feed', 'category' => 'anime', 'name' => 'Tokyo Otaku Mode'],
['url' => 'https://www.funimation.com/blog/feed/', 'category' => 'anime', 'name' => 'Funimation Blog'],

// Manga News
['url' => 'https://www.mangaupdates.com/rss.php', 'category' => 'manga', 'name' => 'Manga Updates'],
['url' => 'https://www.mangareader.net/rss', 'category' => 'manga', 'name' => 'Manga Reader'],
['url' => 'https://www.viz.com/feed', 'category' => 'manga', 'name' => 'VIZ Media'],
['url' => 'https://kodansha.us/feed/', 'category' => 'manga', 'name' => 'Kodansha Comics'],

// JRPG News
['url' => 'https://www.rpgsite.net/feeds/news', 'category' => 'jrpg', 'name' => 'RPG Site'],
['url' => 'https://www.rpgfan.com/feed/', 'category' => 'jrpg', 'name' => 'RPGFan'],
['url' => 'https://gematsu.com/feed', 'category' => 'jrpg', 'name' => 'Gematsu'],
['url' => 'https://www.siliconera.com/feed/', 'category' => 'jrpg', 'name' => 'Siliconera'],

// Final Fantasy
['url' => 'https://www.finalfantasyxiv.com/na/news/rss', 'category' => 'final_fantasy', 'name' => 'FFXIV Official'],
['url' => 'https://www.novacrystallis.com/feed/', 'category' => 'final_fantasy', 'name' => 'Nova Crystallis'],
['url' => 'https://finalfantasy.fandom.com/wiki/Special:RecentChanges?feed=rss', 'category' => 'final_fantasy', 'name' => 'FF Wiki'],
['url' => 'https://www.finalfantasyunion.com/news/feed/', 'category' => 'final_fantasy', 'name' => 'FF Union'],

// Fighting Games
['url' => 'https://www.eventhubs.com/feeds/latest/', 'category' => 'fighting_games', 'name' => 'EventHubs'],
['url' => 'https://www.shoryuken.com/feed/', 'category' => 'fighting_games', 'name' => 'Shoryuken'],
['url' => 'https://www.fgcnow.com/feed/', 'category' => 'fighting_games', 'name' => 'FGC Now'],
['url' => 'https://dreamcancel.com/feed/', 'category' => 'fighting_games', 'name' => 'Dream Cancel'],

// Retro Gaming
['url' => 'https://www.retrorgb.com/feed/', 'category' => 'retro_gaming', 'name' => 'RetroRGB'],
['url' => 'https://www.racketboy.com/feed', 'category' => 'retro_gaming', 'name' => 'Racketboy'],
['url' => 'https://www.retrogamer.net/feed/', 'category' => 'retro_gaming', 'name' => 'Retro Gamer'],
['url' => 'https://www.retronauts.com/feed/', 'category' => 'retro_gaming', 'name' => 'Retronauts'],

// Speedrunning
['url' => 'https://www.speedrun.com/news/rss', 'category' => 'speedrunning', 'name' => 'Speedrun.com'],
['url' => 'https://gdqstatus.com/feed/', 'category' => 'speedrunning', 'name' => 'GDQ Status'],
['url' => 'https://www.thesgf.com/feed/', 'category' => 'speedrunning', 'name' => 'Speedgaming'],
['url' => 'https://knowyourmeme.com/speedrunning/feed', 'category' => 'speedrunning', 'name' => 'Speedrun Memes'],

// Gaming Collections
['url' => 'https://www.pricecharting.com/feed', 'category' => 'gaming_collections', 'name' => 'Price Charting'],
['url' => 'https://www.rarityguide.com/rss/', 'category' => 'gaming_collections', 'name' => 'Rarity Guide'],
['url' => 'https://www.vgcollector.com/feed/', 'category' => 'gaming_collections', 'name' => 'VG Collector'],
['url' => 'https://www.gamevaluenow.com/feed', 'category' => 'gaming_collections', 'name' => 'Game Value Now'],

// Gaming Modding
['url' => 'https://www.nexusmods.com/rss/news', 'category' => 'gaming_mods', 'name' => 'Nexus Mods'],
['url' => 'https://www.moddb.com/rss/news', 'category' => 'gaming_mods', 'name' => 'ModDB'],
['url' => 'https://gamebanana.com/rss/feeds/news', 'category' => 'gaming_mods', 'name' => 'GameBanana'],
['url' => 'https://www.pcgamingwiki.com/feed.rss', 'category' => 'gaming_mods', 'name' => 'PC Gaming Wiki'],
// Add these to your existing getKnownFeeds() array

// Recipe Blogs
['url' => 'https://www.seriouseats.com/recipes.rss', 'category' => 'recipe_blogs', 'name' => 'Serious Eats Recipes'],
['url' => 'https://smittenkitchen.com/feed/', 'category' => 'recipe_blogs', 'name' => 'Smitten Kitchen'],
['url' => 'https://www.simplyrecipes.com/feed/', 'category' => 'recipe_blogs', 'name' => 'Simply Recipes'],
['url' => 'https://www.thekitchn.com/main.rss', 'category' => 'recipe_blogs', 'name' => 'The Kitchn'],

// Baking and Pastry
['url' => 'https://www.kingarthurbaking.com/blog/feed', 'category' => 'baking', 'name' => 'King Arthur Baking'],
['url' => 'https://joythebaker.com/feed/', 'category' => 'baking', 'name' => 'Joy the Baker'],
['url' => 'https://www.handletheheat.com/feed/', 'category' => 'baking', 'name' => 'Handle the Heat'],
['url' => 'https://www.bakingbusiness.com/rss/', 'category' => 'baking', 'name' => 'Baking Business'],

// International Cuisine
['url' => 'https://www.chinasichuanfood.com/feed/', 'category' => 'international_cuisine', 'name' => 'China Sichuan Food'],
['url' => 'https://www.indianhealthyrecipes.com/feed/', 'category' => 'international_cuisine', 'name' => 'Indian Healthy Recipes'],
['url' => 'https://www.justonecookbook.com/feed/', 'category' => 'international_cuisine', 'name' => 'Just One Cookbook'],
['url' => 'https://www.mediterraneanliving.com/feed/', 'category' => 'international_cuisine', 'name' => 'Mediterranean Living'],

// Food Science
['url' => 'https://www.sciencedaily.com/rss/plants_animals/food.xml', 'category' => 'food_science', 'name' => 'ScienceDaily Food'],
['url' => 'https://www.foodnavigator.com/rss/feed.xml', 'category' => 'food_science', 'name' => 'Food Navigator'],
['url' => 'https://www.ift.org/news-and-publications/food-technology-magazine/rss', 'category' => 'food_science', 'name' => 'Institute of Food Technologists'],
['url' => 'https://www.newfoodmagazine.com/feed/', 'category' => 'food_science', 'name' => 'New Food Magazine'],

// Restaurant News
['url' => 'https://www.restaurantbusinessonline.com/rss.xml', 'category' => 'restaurant_news', 'name' => 'Restaurant Business'],
['url' => 'https://www.qsrmagazine.com/rss.xml', 'category' => 'restaurant_news', 'name' => 'QSR Magazine'],
['url' => 'https://www.nrn.com/rss.xml', 'category' => 'restaurant_news', 'name' => 'Nation\'s Restaurant News'],
['url' => 'https://www.restaurantnews.com/feed/', 'category' => 'restaurant_news', 'name' => 'Restaurant News Resource'],

// Wine and Spirits
['url' => 'https://www.winespectator.com/rss/news', 'category' => 'wine_spirits', 'name' => 'Wine Spectator'],
['url' => 'https://www.whiskyadvocate.com/feed/', 'category' => 'wine_spirits', 'name' => 'Whisky Advocate'],
['url' => 'https://vinepair.com/feed/', 'category' => 'wine_spirits', 'name' => 'VinePair'],
['url' => 'https://www.thespruceeats.com/wine-spirits-4162467/rss', 'category' => 'wine_spirits', 'name' => 'The Spruce Eats Spirits'],

// Craft Beer
['url' => 'https://www.craftbeer.com/feed', 'category' => 'craft_beer', 'name' => 'CraftBeer.com'],
['url' => 'https://www.goodbeerhunting.com/feed', 'category' => 'craft_beer', 'name' => 'Good Beer Hunting'],
['url' => 'https://www.beeradvocate.com/rss/', 'category' => 'craft_beer', 'name' => 'Beer Advocate'],
['url' => 'https://www.porchdrinking.com/feed/', 'category' => 'craft_beer', 'name' => 'PorchDrinking'],

// Coffee and Tea
['url' => 'https://sprudge.com/feed', 'category' => 'coffee_tea', 'name' => 'Sprudge'],
['url' => 'https://www.perfectdailygrind.com/feed/', 'category' => 'coffee_tea', 'name' => 'Perfect Daily Grind'],
['url' => 'https://www.worldoftea.org/feed/', 'category' => 'coffee_tea', 'name' => 'World of Tea'],
['url' => 'https://www.teajourney.pub/feed/', 'category' => 'coffee_tea', 'name' => 'Tea Journey'],

// Food Photography
['url' => 'https://foodphotographyblog.com/feed/', 'category' => 'food_photography', 'name' => 'Food Photography Blog'],
['url' => 'https://twolovesstudio.com/feed/', 'category' => 'food_photography', 'name' => 'Two Loves Studio'],
['url' => 'https://www.wearechefs.com/feed/', 'category' => 'food_photography', 'name' => 'We Are Chefs'],
['url' => 'https://foodphotographyschool.com/feed/', 'category' => 'food_photography', 'name' => 'Food Photography School'],

// Sustainable Food
['url' => 'https://civileats.com/feed/', 'category' => 'sustainable_food', 'name' => 'Civil Eats'],
['url' => 'https://sustainablefoodtrust.org/feed/', 'category' => 'sustainable_food', 'name' => 'Sustainable Food Trust'],
['url' => 'https://foodtank.com/feed/', 'category' => 'sustainable_food', 'name' => 'Food Tank'],
['url' => 'https://www.sustainablefoodlab.org/feed/', 'category' => 'sustainable_food', 'name' => 'Sustainable Food Lab'],

// Food Safety
['url' => 'https://www.foodsafetynews.com/feed/', 'category' => 'food_safety', 'name' => 'Food Safety News'],
['url' => 'https://www.fda.gov/about-fda/contact-fda/stay-informed/rss-feeds/food-safety-rss.xml', 'category' => 'food_safety', 'name' => 'FDA Food Safety'],
['url' => 'https://www.foodsafetymagazine.com/feed/', 'category' => 'food_safety', 'name' => 'Food Safety Magazine'],
['url' => 'https://www.food-safety.com/rss/', 'category' => 'food_safety', 'name' => 'Food Safety Tech'],

// Food Business
['url' => 'https://www.foodbusinessnews.net/rss/', 'category' => 'food_business', 'name' => 'Food Business News'],
['url' => 'https://www.foodindustryexecutive.com/feed/', 'category' => 'food_business', 'name' => 'Food Industry Executive'],
['url' => 'https://www.foodmanufacturing.com/rss/', 'category' => 'food_business', 'name' => 'Food Manufacturing'],
['url' => 'https://www.fooddive.com/feeds/news/', 'category' => 'food_business', 'name' => 'Food Dive'],

// Culinary Education
['url' => 'https://www.culinaryschools.org/feed/', 'category' => 'culinary_education', 'name' => 'Culinary Schools'],
['url' => 'https://www.chefs.edu/feed/', 'category' => 'culinary_education', 'name' => 'Le Cordon Bleu'],
['url' => 'https://www.iceculinary.com/blog/feed/', 'category' => 'culinary_education', 'name' => 'Institute of Culinary Education'],
['url' => 'https://www.escoffier.edu/blog/feed/', 'category' => 'culinary_education', 'name' => 'Auguste Escoffier School'],

// Fermentation and Preservation
['url' => 'https://www.fermentingforfoodies.com/feed/', 'category' => 'fermentation', 'name' => 'Fermenting for Foodies'],
['url' => 'https://phickle.com/feed/', 'category' => 'fermentation', 'name' => 'Phickle'],
['url' => 'https://www.culturesforhealth.com/learn/feed/', 'category' => 'fermentation', 'name' => 'Cultures for Health'],
['url' => 'https://www.wildfermentation.com/feed/', 'category' => 'fermentation', 'name' => 'Wild Fermentation'],

// Food History
['url' => 'https://www.foodtimeline.org/feed/', 'category' => 'food_history', 'name' => 'Food Timeline'],
['url' => 'https://www.atlasobscura.com/categories/food-history/rss', 'category' => 'food_history', 'name' => 'Atlas Obscura Food'],
['url' => 'https://www.foodhistorynews.com/feed/', 'category' => 'food_history', 'name' => 'Food History News'],
['url' => 'https://www.historicalfoods.com/feed/', 'category' => 'food_history', 'name' => 'Historical Foods'],
// Add these to your existing getKnownFeeds() array

// Internet Archive Collections
['url' => 'https://archive.org/services/collection-rss.php', 'category' => 'internet_archive', 'name' => 'Internet Archive Main'],
['url' => 'https://archive.org/services/collection-rss.php?collection=texts', 'category' => 'internet_archive', 'name' => 'Internet Archive Books'],
['url' => 'https://archive.org/services/collection-rss.php?collection=audio', 'category' => 'internet_archive', 'name' => 'Internet Archive Audio'],
['url' => 'https://archive.org/services/collection-rss.php?collection=software', 'category' => 'internet_archive', 'name' => 'Internet Archive Software'],

// Digital Libraries
['url' => 'https://www.loc.gov/rss/latest/', 'category' => 'digital_libraries', 'name' => 'Library of Congress'],
['url' => 'https://www.europeana.eu/en/rss', 'category' => 'digital_libraries', 'name' => 'Europeana'],
['url' => 'https://www.wdl.org/en/rss/', 'category' => 'digital_libraries', 'name' => 'World Digital Library'],
['url' => 'https://dp.la/rss', 'category' => 'digital_libraries', 'name' => 'Digital Public Library of America'],

// Academic Archives
['url' => 'https://arxiv.org/rss/latest', 'category' => 'academic_archives', 'name' => 'arXiv Latest'],
['url' => 'https://www.jstor.org/feed/', 'category' => 'academic_archives', 'name' => 'JSTOR Daily'],
['url' => 'https://www.academia.edu/rss/', 'category' => 'academic_archives', 'name' => 'Academia.edu'],
['url' => 'https://www.sciencedirect.com/rss/open-access', 'category' => 'academic_archives', 'name' => 'ScienceDirect Open Access'],

// Historical Collections
['url' => 'https://www.britishmuseum.org/research/research-news.rss', 'category' => 'historical_collections', 'name' => 'British Museum Research'],
['url' => 'https://www.metmuseum.org/rss', 'category' => 'historical_collections', 'name' => 'Metropolitan Museum'],
['url' => 'https://www.smithsonianmag.com/rss/history/', 'category' => 'historical_collections', 'name' => 'Smithsonian History'],
['url' => 'https://www.nationalarchives.gov.uk/rss/', 'category' => 'historical_collections', 'name' => 'UK National Archives'],

// Digital Preservation
['url' => 'https://blogs.loc.gov/thesignal/feed/', 'category' => 'digital_preservation', 'name' => 'Library of Congress Digital'],
['url' => 'https://dpconline.org/feed', 'category' => 'digital_preservation', 'name' => 'Digital Preservation Coalition'],
['url' => 'https://www.lockss.org/feed/', 'category' => 'digital_preservation', 'name' => 'LOCKSS Program'],
['url' => 'https://preservica.com/blog/feed', 'category' => 'digital_preservation', 'name' => 'Preservica'],

// Open Access Journals
['url' => 'https://doaj.org/feed', 'category' => 'open_access', 'name' => 'Directory of Open Access Journals'],
['url' => 'https://www.plos.org/feed', 'category' => 'open_access', 'name' => 'PLOS'],
['url' => 'https://www.biomedcentral.com/rss', 'category' => 'open_access', 'name' => 'BioMed Central'],
['url' => 'https://www.frontiersin.org/rss', 'category' => 'open_access', 'name' => 'Frontiers'],

// Media Archives
['url' => 'https://www.bfi.org.uk/rss', 'category' => 'media_archives', 'name' => 'British Film Institute'],
['url' => 'https://www.loc.gov/rss/film-and-videos/', 'category' => 'media_archives', 'name' => 'LoC Film & Video'],
['url' => 'https://www.npr.org/rss/archive/archive_1.xml', 'category' => 'media_archives', 'name' => 'NPR Archives'],
['url' => 'https://www.americanarchive.org/rss', 'category' => 'media_archives', 'name' => 'American Archive of Public Broadcasting'],

// Map Collections
['url' => 'https://www.davidrumsey.com/rss/', 'category' => 'map_collections', 'name' => 'David Rumsey Map Collection'],
['url' => 'https://www.oldmapsonline.org/feed/', 'category' => 'map_collections', 'name' => 'Old Maps Online'],
['url' => 'https://www.loc.gov/rss/maps/', 'category' => 'map_collections', 'name' => 'LoC Geography & Maps'],
['url' => 'https://www.nationalarchives.gov.uk/maps/rss/', 'category' => 'map_collections', 'name' => 'UK Archives Maps'],

// Web Archives
['url' => 'https://blog.archive.org/category/wayback-machine/feed/', 'category' => 'web_archives', 'name' => 'Wayback Machine'],
['url' => 'https://webarchive.org.uk/rss/recent', 'category' => 'web_archives', 'name' => 'UK Web Archive'],
['url' => 'https://www.webharvest.gov/rss', 'category' => 'web_archives', 'name' => 'US Government Web Archive'],
['url' => 'https://pandora.nla.gov.au/rss/', 'category' => 'web_archives', 'name' => 'PANDORA Archive'],

// Newspaper Archives
['url' => 'https://chroniclingamerica.loc.gov/rss/', 'category' => 'newspaper_archives', 'name' => 'Chronicling America'],
['url' => 'https://www.britishnewspaperarchive.co.uk/rss', 'category' => 'newspaper_archives', 'name' => 'British Newspaper Archive'],
['url' => 'https://trove.nla.gov.au/rss/newspapers', 'category' => 'newspaper_archives', 'name' => 'Trove Newspapers'],
['url' => 'https://www.europeana.eu/en/newspapers/rss', 'category' => 'newspaper_archives', 'name' => 'Europeana Newspapers'],

// Photography Archives
['url' => 'https://www.shorpy.com/rss.xml', 'category' => 'photo_archives', 'name' => 'Shorpy Historical Photos'],
['url' => 'https://www.loc.gov/rss/photos/', 'category' => 'photo_archives', 'name' => 'LoC Prints & Photos'],
['url' => 'https://www.gettyimages.com/feed/archives', 'category' => 'photo_archives', 'name' => 'Getty Archive'],
['url' => 'https://www.nationalmediamuseum.org.uk/rss', 'category' => 'photo_archives', 'name' => 'National Media Museum'],

// Manuscript Collections
['url' => 'https://www.bl.uk/rss/manuscripts', 'category' => 'manuscripts', 'name' => 'British Library Manuscripts'],
['url' => 'https://www.vhmml.org/rss', 'category' => 'manuscripts', 'name' => 'Virtual Hill Museum'],
['url' => 'https://digital.bodleian.ox.ac.uk/rss', 'category' => 'manuscripts', 'name' => 'Bodleian Digital'],
['url' => 'https://www.e-codices.unifr.ch/rss', 'category' => 'manuscripts', 'name' => 'e-codices'],

// Government Documents
['url' => 'https://www.govinfo.gov/rss/latest.xml', 'category' => 'govt_documents', 'name' => 'GovInfo Latest'],
['url' => 'https://www.archives.gov/feeds/whatsnew.xml', 'category' => 'govt_documents', 'name' => 'National Archives News'],
['url' => 'https://www.congress.gov/rss/most-viewed.xml', 'category' => 'govt_documents', 'name' => 'Congress.gov Most Viewed'],
['url' => 'https://www.gao.gov/rss/reports.rss', 'category' => 'govt_documents', 'name' => 'GAO Reports'],

// Scientific Data Archives
['url' => 'https://www.ncbi.nlm.nih.gov/feed/', 'category' => 'scientific_archives', 'name' => 'NCBI Latest'],
['url' => 'https://zenodo.org/feed/', 'category' => 'scientific_archives', 'name' => 'Zenodo'],
['url' => 'https://datadryad.org/feed/', 'category' => 'scientific_archives', 'name' => 'Dryad'],
['url' => 'https://figshare.com/rss', 'category' => 'scientific_archives', 'name' => 'Figshare'],

// Music Archives
['url' => 'https://digitalcollections.nypl.org/items/rss?collection=rodgers-and-hammerstein-archives-of-recorded-sound', 'category' => 'music_archives', 'name' => 'NYPL Music'],
['url' => 'https://www.loc.gov/rss/music/', 'category' => 'music_archives', 'name' => 'LoC Music'],
['url' => 'https://www.bl.uk/rss/sound', 'category' => 'music_archives', 'name' => 'British Library Sound'],
['url' => 'https://www.europeana.eu/en/music/rss', 'category' => 'music_archives', 'name' => 'Europeana Music'],
// Add these to your existing getKnownFeeds() array

// Official Security Advisories
['url' => 'https://www.us-cert.gov/ncas/alerts.xml', 'category' => 'security_advisories', 'name' => 'US-CERT Alerts'],
['url' => 'https://www.cisa.gov/uscert/ncas/alerts.xml', 'category' => 'security_advisories', 'name' => 'CISA Alerts'],
['url' => 'https://nvd.nist.gov/feeds/xml/cve/misc/nvd-rss.xml', 'category' => 'security_advisories', 'name' => 'NVD Vulnerabilities'],
['url' => 'https://www.microsoft.com/security/blog/feed/', 'category' => 'security_advisories', 'name' => 'Microsoft Security'],

// Security Research
['url' => 'https://research.google/feed/', 'category' => 'security_research', 'name' => 'Google Research'],
['url' => 'https://www.virusbulletin.com/rss', 'category' => 'security_research', 'name' => 'Virus Bulletin'],
['url' => 'https://googleprojectzero.blogspot.com/feeds/posts/default', 'category' => 'security_research', 'name' => 'Project Zero'],
['url' => 'https://citizenlab.ca/feed/', 'category' => 'security_research', 'name' => 'Citizen Lab'],

// Responsible Disclosure
['url' => 'https://hackerone.com/hacktivity.rss', 'category' => 'responsible_disclosure', 'name' => 'HackerOne'],
['url' => 'https://bugcrowd.com/feed', 'category' => 'responsible_disclosure', 'name' => 'Bugcrowd'],
['url' => 'https://blogs.apache.org/security/feed/', 'category' => 'responsible_disclosure', 'name' => 'Apache Security'],
['url' => 'https://www.mozilla.org/security/advisories/feed/', 'category' => 'responsible_disclosure', 'name' => 'Mozilla Security'],

// Security Best Practices
['url' => 'https://www.sans.org/blog/feed/', 'category' => 'security_practices', 'name' => 'SANS Blog'],
['url' => 'https://www.schneier.com/feed/atom/', 'category' => 'security_practices', 'name' => 'Schneier on Security'],
['url' => 'https://www.troyhunt.com/rss/', 'category' => 'security_practices', 'name' => 'Troy Hunt'],
['url' => 'https://www.darkreading.com/rss_simple.asp', 'category' => 'security_practices', 'name' => 'Dark Reading'],

// Security Tools
['url' => 'https://portswigger.net/blog/rss', 'category' => 'security_tools', 'name' => 'PortSwigger Research'],
['url' => 'https://www.wireshark.org/news/rss', 'category' => 'security_tools', 'name' => 'Wireshark News'],
['url' => 'https://nmap.org/rss.xml', 'category' => 'security_tools', 'name' => 'Nmap Security'],
['url' => 'https://www.metasploit.com/blog.xml', 'category' => 'security_tools', 'name' => 'Metasploit Blog'],
// Add these to your existing getKnownFeeds() array

// Housing Market News
['url' => 'https://www.realtor.com/news/feed/', 'category' => 'housing_market', 'name' => 'Realtor.com News'],
['url' => 'https://www.zillow.com/blog/feed/', 'category' => 'housing_market', 'name' => 'Zillow Research'],
['url' => 'https://www.redfin.com/news/feed/', 'category' => 'housing_market', 'name' => 'Redfin News'],
['url' => 'https://www.nar.realtor/rss.xml', 'category' => 'housing_market', 'name' => 'National Association of Realtors'],

// Real Estate Analysis
['url' => 'https://journal.firsttuesday.us/feed/', 'category' => 'real_estate_analysis', 'name' => 'First Tuesday Journal'],
['url' => 'https://www.propertyshark.com/Real-Estate-Reports/feed/', 'category' => 'real_estate_analysis', 'name' => 'PropertyShark Reports'],
['url' => 'https://www.curbed.com/rss/index.xml', 'category' => 'real_estate_analysis', 'name' => 'Curbed'],
['url' => 'https://www.inman.com/feed/', 'category' => 'real_estate_analysis', 'name' => 'Inman News'],

// Mortgage News
['url' => 'https://www.mortgagenewsdaily.com/rss.aspx', 'category' => 'mortgage_news', 'name' => 'Mortgage News Daily'],
['url' => 'https://www.housingwire.com/feed/', 'category' => 'mortgage_news', 'name' => 'HousingWire'],
['url' => 'https://www.nationalmortgagenews.com/feed', 'category' => 'mortgage_news', 'name' => 'National Mortgage News'],
['url' => 'https://www.mortgageorb.com/feed', 'category' => 'mortgage_news', 'name' => 'Mortgage Orb'],

// Construction Industry
['url' => 'https://www.constructiondive.com/feeds/news/', 'category' => 'construction', 'name' => 'Construction Dive'],
['url' => 'https://www.enr.com/rss/', 'category' => 'construction', 'name' => 'Engineering News-Record'],
['url' => 'https://www.constructionnews.co.uk/feed', 'category' => 'construction', 'name' => 'Construction News'],
['url' => 'https://www.bdcnetwork.com/rss.xml', 'category' => 'construction', 'name' => 'Building Design + Construction'],

// Market Research Firms
['url' => 'https://www.nielsen.com/feed/', 'category' => 'market_research', 'name' => 'Nielsen Insights'],
['url' => 'https://www.ipsos.com/en/rss.xml', 'category' => 'market_research', 'name' => 'Ipsos Research'],
['url' => 'https://www.kantarworldpanel.com/global/rss', 'category' => 'market_research', 'name' => 'Kantar Worldpanel'],
['url' => 'https://www.gartner.com/rss', 'category' => 'market_research', 'name' => 'Gartner Research'],

// Consumer Behavior
['url' => 'https://www.consumeraffairs.com/news/feed/', 'category' => 'consumer_behavior', 'name' => 'Consumer Affairs'],
['url' => 'https://www.marketingcharts.com/feed', 'category' => 'consumer_behavior', 'name' => 'Marketing Charts'],
['url' => 'https://www.pewresearch.org/feed/', 'category' => 'consumer_behavior', 'name' => 'Pew Research'],
['url' => 'https://www.gallup.com/rss/all-gallup-headlines.aspx', 'category' => 'consumer_behavior', 'name' => 'Gallup News'],

// Economic Indicators
['url' => 'https://www.bea.gov/news/rss.xml', 'category' => 'economic_indicators', 'name' => 'Bureau of Economic Analysis'],
['url' => 'https://www.bls.gov/feed/news.rss', 'category' => 'economic_indicators', 'name' => 'Bureau of Labor Statistics'],
['url' => 'https://www.census.gov/newsroom/rss-feeds.xml', 'category' => 'economic_indicators', 'name' => 'US Census Bureau'],
['url' => 'https://www.conference-board.org/rss/', 'category' => 'economic_indicators', 'name' => 'The Conference Board'],

// Real Estate Investment
['url' => 'https://www.reit.com/news/rss', 'category' => 'real_estate_investment', 'name' => 'REIT.com'],
['url' => 'https://www.biggerpockets.com/blog/feed', 'category' => 'real_estate_investment', 'name' => 'BiggerPockets'],
['url' => 'https://www.nreionline.com/rss.xml', 'category' => 'real_estate_investment', 'name' => 'National Real Estate Investor'],
['url' => 'https://www.crowdstreet.com/blog/feed/', 'category' => 'real_estate_investment', 'name' => 'CrowdStreet'],

// Property Management
['url' => 'https://www.propertymanagerinsider.com/feed/', 'category' => 'property_management', 'name' => 'Property Manager Insider'],
['url' => 'https://www.multifamilyexecutive.com/rss/', 'category' => 'property_management', 'name' => 'Multifamily Executive'],
['url' => 'https://www.rentprep.com/blog/feed/', 'category' => 'property_management', 'name' => 'RentPrep'],
['url' => 'https://www.propertyware.com/blog/feed/', 'category' => 'property_management', 'name' => 'Propertyware'],

// Commercial Real Estate
['url' => 'https://www.globest.com/feed/', 'category' => 'commercial_real_estate', 'name' => 'GlobeSt'],
['url' => 'https://www.costar.com/rss/', 'category' => 'commercial_real_estate', 'name' => 'CoStar News'],
['url' => 'https://commercialobserver.com/feed/', 'category' => 'commercial_real_estate', 'name' => 'Commercial Observer'],
['url' => 'https://www.cpexecutive.com/feed/', 'category' => 'commercial_real_estate', 'name' => 'Commercial Property Executive'],

// Market Demographics
['url' => 'https://www.demographic-research.org/rss.xml', 'category' => 'market_demographics', 'name' => 'Demographic Research'],
['url' => 'https://www.prb.org/rss/', 'category' => 'market_demographics', 'name' => 'Population Reference Bureau'],
['url' => 'https://www.pewresearch.org/topics/demographics/feed/', 'category' => 'market_demographics', 'name' => 'Pew Demographics'],
['url' => 'https://www.census.gov/library/publications/rss-feeds.html', 'category' => 'market_demographics', 'name' => 'Census Publications'],

// Urban Development
['url' => 'https://www.citylab.com/feed/', 'category' => 'urban_development', 'name' => 'CityLab'],
['url' => 'https://urbanland.uli.org/feed/', 'category' => 'urban_development', 'name' => 'Urban Land Magazine'],
['url' => 'https://www.planetizen.com/feed/news', 'category' => 'urban_development', 'name' => 'Planetizen'],
['url' => 'https://www.smartcitiesdive.com/feeds/news/', 'category' => 'urban_development', 'name' => 'Smart Cities Dive'],
// Add these to your existing getKnownFeeds() array

// Forestry News
['url' => 'https://www.forestry.gov.uk/rss/news', 'category' => 'forestry_news', 'name' => 'UK Forestry Commission'],
['url' => 'https://www.fs.usda.gov/rss/forests', 'category' => 'forestry_news', 'name' => 'US Forest Service'],
['url' => 'https://www.canadianforestry.com/feed/', 'category' => 'forestry_news', 'name' => 'Canadian Forestry'],
['url' => 'https://www.forestindustry.com/rss/', 'category' => 'forestry_news', 'name' => 'Forest Industry News'],

// Forest Science
['url' => 'https://academic.oup.com/forestry/rss', 'category' => 'forest_science', 'name' => 'Oxford Forestry Journal'],
['url' => 'https://www.sciencedaily.com/rss/plants_animals/forests.xml', 'category' => 'forest_science', 'name' => 'ScienceDaily Forests'],
['url' => 'https://www.frontiersin.org/journals/forests-and-global-change/rss', 'category' => 'forest_science', 'name' => 'Frontiers in Forests'],
['url' => 'https://www.nature.com/subjects/forestry.rss', 'category' => 'forest_science', 'name' => 'Nature Forestry'],

// Silviculture
['url' => 'https://www.silviculture.com/feed/', 'category' => 'silviculture', 'name' => 'Silviculture Magazine'],
['url' => 'https://www.forestresearch.gov.uk/rss/silviculture', 'category' => 'silviculture', 'name' => 'Forest Research UK'],
['url' => 'https://silviculture.org/news/feed/', 'category' => 'silviculture', 'name' => 'Silviculture Network'],
['url' => 'https://www.canadiansilviculture.com/feed/', 'category' => 'silviculture', 'name' => 'Canadian Silviculture'],

// Forest Management
['url' => 'https://www.fao.org/forestry/rss/', 'category' => 'forest_management', 'name' => 'FAO Forestry'],
['url' => 'https://www.forestmanagement.com/feed/', 'category' => 'forest_management', 'name' => 'Forest Management'],
['url' => 'https://www.foreststewardship.org/feed/', 'category' => 'forest_management', 'name' => 'Forest Stewardship Council'],
['url' => 'https://www.sustainableforestry.net/feed/', 'category' => 'forest_management', 'name' => 'Sustainable Forestry'],

// Timber Industry
['url' => 'https://www.woodbusiness.ca/feed/', 'category' => 'timber_industry', 'name' => 'Wood Business'],
['url' => 'https://www.timberbiz.com.au/feed/', 'category' => 'timber_industry', 'name' => 'Timber Biz'],
['url' => 'https://www.lesprom.com/rss/', 'category' => 'timber_industry', 'name' => 'Lesprom Network'],
['url' => 'https://www.ttjonline.com/rss/', 'category' => 'timber_industry', 'name' => 'Timber Trades Journal'],

// Forest Conservation
['url' => 'https://www.globalforestwatch.org/feed/', 'category' => 'forest_conservation', 'name' => 'Global Forest Watch'],
['url' => 'https://forestsnews.cifor.org/feed', 'category' => 'forest_conservation', 'name' => 'CIFOR Forest News'],
['url' => 'https://www.rainforest-alliance.org/feed/', 'category' => 'forest_conservation', 'name' => 'Rainforest Alliance'],
['url' => 'https://news.mongabay.com/feed/forests', 'category' => 'forest_conservation', 'name' => 'Mongabay Forests'],

// Urban Forestry
['url' => 'https://www.urbanforestry.com/feed/', 'category' => 'urban_forestry', 'name' => 'Urban Forestry News'],
['url' => 'https://www.treepeople.org/feed/', 'category' => 'urban_forestry', 'name' => 'TreePeople'],
['url' => 'https://www.cityoftrees.org/feed/', 'category' => 'urban_forestry', 'name' => 'City of Trees'],
['url' => 'https://www.urban-forestry.com/feed/', 'category' => 'urban_forestry', 'name' => 'Society of Municipal Arborists'],

// Forest Products
['url' => 'https://www.woodworkingnetwork.com/rss.xml', 'category' => 'forest_products', 'name' => 'Woodworking Network'],
['url' => 'https://www.forestproducts.org/feed/', 'category' => 'forest_products', 'name' => 'Forest Products Association'],
['url' => 'https://www.woodproducts.fi/rss/', 'category' => 'forest_products', 'name' => 'Wood Products Finland'],
['url' => 'https://www.pulpandpaper.com/feed/', 'category' => 'forest_products', 'name' => 'Pulp and Paper'],

// Forest Health
['url' => 'https://www.foresthealth.org/feed/', 'category' => 'forest_health', 'name' => 'Forest Health News'],
['url' => 'https://www.fs.fed.us/foresthealth/rss/', 'category' => 'forest_health', 'name' => 'USFS Forest Health'],
['url' => 'https://forestpathology.org/feed/', 'category' => 'forest_health', 'name' => 'Forest Pathology'],
['url' => 'https://www.forestpests.org/feed/', 'category' => 'forest_health', 'name' => 'Forest Pests'],

// Forest Fire Management
['url' => 'https://www.nifc.gov/RSS/nifc.xml', 'category' => 'forest_fire', 'name' => 'National Interagency Fire Center'],
['url' => 'https://firewise.org/feed/', 'category' => 'forest_fire', 'name' => 'Firewise USA'],
['url' => 'https://www.wildfire.org/rss/', 'category' => 'forest_fire', 'name' => 'International Association of Wildland Fire'],
['url' => 'https://www.fireecology.org/feed/', 'category' => 'forest_fire', 'name' => 'Fire Ecology'],

// Forest Economics
['url' => 'https://www.foresteconomics.org/feed/', 'category' => 'forest_economics', 'name' => 'Forest Economics'],
['url' => 'https://www.timbereconomics.com/feed/', 'category' => 'forest_economics', 'name' => 'Timber Economics'],
['url' => 'https://www.forestbusinessnetwork.com/feed/', 'category' => 'forest_economics', 'name' => 'Forest Business Network'],
['url' => 'https://www.forestindustries.eu/rss/', 'category' => 'forest_economics', 'name' => 'Forest Industries'],

// Agroforestry
['url' => 'https://www.agroforestry.org/feed/', 'category' => 'agroforestry', 'name' => 'Association for Temperate Agroforestry'],
['url' => 'https://www.worldagroforestry.org/news/rss.xml', 'category' => 'agroforestry', 'name' => 'World Agroforestry'],
['url' => 'https://www.agforward.eu/feed/', 'category' => 'agroforestry', 'name' => 'AGFORWARD'],
['url' => 'https://www.centerforagroforestry.org/feed/', 'category' => 'agroforestry', 'name' => 'Center for Agroforestry'],

// Forest Recreation
['url' => 'https://www.americantrails.org/feed', 'category' => 'forest_recreation', 'name' => 'American Trails'],
['url' => 'https://www.forestcamping.com/feed/', 'category' => 'forest_recreation', 'name' => 'Forest Camping'],
['url' => 'https://www.nationalforests.org/blog/rss', 'category' => 'forest_recreation', 'name' => 'National Forest Foundation'],
['url' => 'https://www.hikingproject.com/rss/', 'category' => 'forest_recreation', 'name' => 'Hiking Project'],
// Add these to your existing getKnownFeeds() array

// United States
['url' => 'https://www.nytimes.com/svc/collections/v1/publish/www.nytimes.com/section/us/rss.xml', 'category' => 'usa_news', 'name' => 'New York Times US'],
['url' => 'https://abcnews.go.com/abcnews/usheadlines', 'category' => 'usa_news', 'name' => 'ABC US News'],
['url' => 'https://feeds.npr.org/1001/rss.xml', 'category' => 'usa_news', 'name' => 'NPR US News'],
['url' => 'https://www.pbs.org/newshour/feeds/rss/headlines', 'category' => 'usa_news', 'name' => 'PBS NewsHour'],
['url' => 'https://www.usatoday.com/rss/news/nation/', 'category' => 'usa_news', 'name' => 'USA Today National'],

// Canada
['url' => 'https://www.cbc.ca/cmlink/rss-canada', 'category' => 'canada_news', 'name' => 'CBC Canada'],
['url' => 'https://www.theglobeandmail.com/feed/', 'category' => 'canada_news', 'name' => 'Globe and Mail'],
['url' => 'https://www.ctvnews.ca/rss/canada', 'category' => 'canada_news', 'name' => 'CTV News Canada'],
['url' => 'https://nationalpost.com/feed/rss/canada', 'category' => 'canada_news', 'name' => 'National Post Canada'],
['url' => 'https://www.thestar.com/feed.rss', 'category' => 'canada_news', 'name' => 'Toronto Star'],

// Mexico
['url' => 'https://www.eluniversal.com.mx/rss.xml', 'category' => 'mexico_news', 'name' => 'El Universal'],
['url' => 'https://www.reforma.com/rss/portada.xml', 'category' => 'mexico_news', 'name' => 'Reforma'],
['url' => 'https://www.excelsior.com.mx/rss.xml', 'category' => 'mexico_news', 'name' => 'Excélsior'],
['url' => 'https://www.jornada.com.mx/rss/mexico.xml', 'category' => 'mexico_news', 'name' => 'La Jornada'],
['url' => 'https://www.milenio.com/feed', 'category' => 'mexico_news', 'name' => 'Milenio'],

// US Northeast
['url' => 'https://www.bostonglobe.com/rss/metro', 'category' => 'us_northeast', 'name' => 'Boston Globe'],
['url' => 'https://www.inquirer.com/feed', 'category' => 'us_northeast', 'name' => 'Philadelphia Inquirer'],
['url' => 'https://nypost.com/feed/', 'category' => 'us_northeast', 'name' => 'New York Post'],
['url' => 'https://www.newsday.com/feed', 'category' => 'us_northeast', 'name' => 'Newsday'],

// US Southeast
['url' => 'https://www.ajc.com/feed', 'category' => 'us_southeast', 'name' => 'Atlanta Journal-Constitution'],
['url' => 'https://www.miamiherald.com/news/nation-world/national/feed/', 'category' => 'us_southeast', 'name' => 'Miami Herald'],
['url' => 'https://www.orlandosentinel.com/feed', 'category' => 'us_southeast', 'name' => 'Orlando Sentinel'],
['url' => 'https://www.tennessean.com/feed', 'category' => 'us_southeast', 'name' => 'The Tennessean'],

// US Midwest
['url' => 'https://www.chicagotribune.com/feed', 'category' => 'us_midwest', 'name' => 'Chicago Tribune'],
['url' => 'https://www.detroitnews.com/feed', 'category' => 'us_midwest', 'name' => 'Detroit News'],
['url' => 'https://www.startribune.com/feed', 'category' => 'us_midwest', 'name' => 'Minneapolis Star Tribune'],
['url' => 'https://www.cleveland.com/feed', 'category' => 'us_midwest', 'name' => 'Cleveland.com'],

// US Southwest
['url' => 'https://www.dallasnews.com/feed', 'category' => 'us_southwest', 'name' => 'Dallas Morning News'],
['url' => 'https://www.houstonchronicle.com/feed', 'category' => 'us_southwest', 'name' => 'Houston Chronicle'],
['url' => 'https://www.azcentral.com/feed', 'category' => 'us_southwest', 'name' => 'Arizona Republic'],
['url' => 'https://www.abqjournal.com/feed', 'category' => 'us_southwest', 'name' => 'Albuquerque Journal'],

// US West Coast
['url' => 'https://www.latimes.com/feed', 'category' => 'us_west', 'name' => 'Los Angeles Times'],
['url' => 'https://www.sfchronicle.com/feed', 'category' => 'us_west', 'name' => 'San Francisco Chronicle'],
['url' => 'https://www.seattletimes.com/feed', 'category' => 'us_west', 'name' => 'Seattle Times'],
['url' => 'https://www.oregonlive.com/feed', 'category' => 'us_west', 'name' => 'The Oregonian'],

// Canadian Provinces
['url' => 'https://www.theprovince.com/feed', 'category' => 'canada_provinces', 'name' => 'The Province BC'],
['url' => 'https://calgaryherald.com/feed', 'category' => 'canada_provinces', 'name' => 'Calgary Herald'],
['url' => 'https://montrealgazette.com/feed', 'category' => 'canada_provinces', 'name' => 'Montreal Gazette'],
['url' => 'https://www.thechronicleherald.ca/feed', 'category' => 'canada_provinces', 'name' => 'Halifax Chronicle Herald'],
// Add these to your existing getKnownFeeds() array

// United Kingdom
['url' => 'https://feeds.bbci.co.uk/news/uk/rss.xml', 'category' => 'uk_news', 'name' => 'BBC UK News'],
['url' => 'https://www.theguardian.com/uk/rss', 'category' => 'uk_news', 'name' => 'The Guardian UK'],
['url' => 'https://www.telegraph.co.uk/rss.xml', 'category' => 'uk_news', 'name' => 'The Telegraph'],
['url' => 'https://www.independent.co.uk/rss', 'category' => 'uk_news', 'name' => 'The Independent'],
['url' => 'https://www.thetimes.co.uk/rss', 'category' => 'uk_news', 'name' => 'The Times'],

// Germany
['url' => 'https://www.spiegel.de/international/index.rss', 'category' => 'germany_news', 'name' => 'Der Spiegel International'],
['url' => 'https://www.dw.com/en/rss/news', 'category' => 'germany_news', 'name' => 'Deutsche Welle'],
['url' => 'https://www.faz.net/rss/aktuell/', 'category' => 'germany_news', 'name' => 'Frankfurter Allgemeine'],
['url' => 'https://www.sueddeutsche.de/news.rss', 'category' => 'germany_news', 'name' => 'Süddeutsche Zeitung'],
['url' => 'https://www.zeit.de/index/index/rss.xml', 'category' => 'germany_news', 'name' => 'Die Zeit'],

// France
['url' => 'https://www.lemonde.fr/rss/en_continu.xml', 'category' => 'france_news', 'name' => 'Le Monde'],
['url' => 'https://www.lefigaro.fr/rss/figaro_actualites.xml', 'category' => 'france_news', 'name' => 'Le Figaro'],
['url' => 'https://www.liberation.fr/rss/', 'category' => 'france_news', 'name' => 'Libération'],
['url' => 'https://www.france24.com/en/rss', 'category' => 'france_news', 'name' => 'France 24'],
['url' => 'https://www.leparisien.fr/rss.xml', 'category' => 'france_news', 'name' => 'Le Parisien'],

// Italy
['url' => 'https://www.corriere.it/rss/homepage.xml', 'category' => 'italy_news', 'name' => 'Corriere della Sera'],
['url' => 'https://www.repubblica.it/rss/homepage/rss2.0.xml', 'category' => 'italy_news', 'name' => 'La Repubblica'],
['url' => 'https://www.lastampa.it/rss.xml', 'category' => 'italy_news', 'name' => 'La Stampa'],
['url' => 'https://www.ilsole24ore.com/rss', 'category' => 'italy_news', 'name' => 'Il Sole 24 Ore'],

// Spain
['url' => 'https://elpais.com/rss/elpais/portada.xml', 'category' => 'spain_news', 'name' => 'El País'],
['url' => 'https://www.elmundo.es/rss/portada.xml', 'category' => 'spain_news', 'name' => 'El Mundo'],
['url' => 'https://www.abc.es/rss/feeds/abc_EspanaEspana.xml', 'category' => 'spain_news', 'name' => 'ABC'],
['url' => 'https://www.lavanguardia.com/rss', 'category' => 'spain_news', 'name' => 'La Vanguardia'],

// Scandinavia
['url' => 'https://www.thelocal.se/feeds/rss.xml', 'category' => 'scandinavia_news', 'name' => 'The Local Sweden'],
['url' => 'https://www.thelocal.dk/feeds/rss.xml', 'category' => 'scandinavia_news', 'name' => 'The Local Denmark'],
['url' => 'https://www.thelocal.no/feeds/rss.xml', 'category' => 'scandinavia_news', 'name' => 'The Local Norway'],
['url' => 'https://yle.fi/uutiset/rss/uutiset.rss', 'category' => 'scandinavia_news', 'name' => 'YLE Finland'],

// Eastern Europe
['url' => 'https://www.themoscowtimes.com/rss/news', 'category' => 'eastern_europe', 'name' => 'The Moscow Times'],
['url' => 'https://www.kyivpost.com/feed', 'category' => 'eastern_europe', 'name' => 'Kyiv Post'],
['url' => 'https://www.praguemorning.cz/feed/', 'category' => 'eastern_europe', 'name' => 'Prague Morning'],
['url' => 'https://warszawapoint.pl/feed', 'category' => 'eastern_europe', 'name' => 'Warszawa Point'],

// Benelux
['url' => 'https://www.dutchnews.nl/feed/', 'category' => 'benelux_news', 'name' => 'Dutch News'],
['url' => 'https://www.brusselstimes.com/feed/', 'category' => 'benelux_news', 'name' => 'Brussels Times'],
['url' => 'https://today.rtl.lu/rss', 'category' => 'benelux_news', 'name' => 'RTL Luxembourg'],
['url' => 'https://www.flanderstoday.eu/feed', 'category' => 'benelux_news', 'name' => 'Flanders Today'],

// Mediterranean
['url' => 'https://www.ekathimerini.com/rss', 'category' => 'mediterranean_news', 'name' => 'Kathimerini (Greece)'],
['url' => 'https://www.hurriyetdailynews.com/rss', 'category' => 'mediterranean_news', 'name' => 'Hurriyet (Turkey)'],
['url' => 'https://www.timesofmalta.com/rss', 'category' => 'mediterranean_news', 'name' => 'Times of Malta'],
['url' => 'https://cyprus-mail.com/feed/', 'category' => 'mediterranean_news', 'name' => 'Cyprus Mail'],
// Add these to your existing getKnownFeeds() array

// Japan
['url' => 'https://www3.nhk.or.jp/nhkworld/en/rss/', 'category' => 'japan_news', 'name' => 'NHK World'],
['url' => 'https://www.japantimes.co.jp/feed/', 'category' => 'japan_news', 'name' => 'Japan Times'],
['url' => 'https://mainichi.jp/rss/eng/mainichi.rss', 'category' => 'japan_news', 'name' => 'Mainichi Shimbun'],
['url' => 'https://www.asahi.com/ajw/feed/index.rdf', 'category' => 'japan_news', 'name' => 'Asahi Shimbun'],
['url' => 'https://japantoday.com/feed', 'category' => 'japan_news', 'name' => 'Japan Today'],

// China
['url' => 'https://www.scmp.com/rss/91/feed', 'category' => 'china_news', 'name' => 'South China Morning Post'],
['url' => 'https://chinadaily.com.cn/rss/world_rss.xml', 'category' => 'china_news', 'name' => 'China Daily'],
['url' => 'https://www.globaltimes.cn/rss/index.xml', 'category' => 'china_news', 'name' => 'Global Times'],
['url' => 'https://english.caixin.com/rss/feed.xml', 'category' => 'china_news', 'name' => 'Caixin Global'],
['url' => 'https://www.sixthtone.com/rss', 'category' => 'china_news', 'name' => 'Sixth Tone'],

// South Korea
['url' => 'https://www.koreaherald.com/rss_xml.php', 'category' => 'korea_news', 'name' => 'Korea Herald'],
['url' => 'https://en.yna.co.kr/RSS/news.xml', 'category' => 'korea_news', 'name' => 'Yonhap News'],
['url' => 'https://feeds.hankyung.com/feed/economia.xml', 'category' => 'korea_news', 'name' => 'Korea Economic Daily'],
['url' => 'https://www.koreatimes.co.kr/www/rss/rss.xml', 'category' => 'korea_news', 'name' => 'Korea Times'],

// India
['url' => 'https://timesofindia.indiatimes.com/rssfeeds/1221656.cms', 'category' => 'india_news', 'name' => 'Times of India'],
['url' => 'https://www.thehindu.com/rss/news.rss', 'category' => 'india_news', 'name' => 'The Hindu'],
['url' => 'https://www.ndtv.com/rss/india', 'category' => 'india_news', 'name' => 'NDTV'],
['url' => 'https://indianexpress.com/feed/', 'category' => 'india_news', 'name' => 'Indian Express'],
['url' => 'https://www.hindustantimes.com/rss/india', 'category' => 'india_news', 'name' => 'Hindustan Times'],

// Southeast Asia
['url' => 'https://www.bangkokpost.com/rss/news.xml', 'category' => 'southeast_asia', 'name' => 'Bangkok Post'],
['url' => 'https://www.straitstimes.com/news/asia/rss.xml', 'category' => 'southeast_asia', 'name' => 'Straits Times'],
['url' => 'https://www.thejakartapost.com/rss', 'category' => 'southeast_asia', 'name' => 'Jakarta Post'],
['url' => 'https://vietnamnews.vn/rss/news.rss', 'category' => 'southeast_asia', 'name' => 'Vietnam News'],
['url' => 'https://www.philstar.com/rss/headlines', 'category' => 'southeast_asia', 'name' => 'Philippine Star'],

// Middle East
['url' => 'https://www.haaretz.com/cmlink/1.4605045', 'category' => 'middle_east', 'name' => 'Haaretz'],
['url' => 'https://www.aljazeera.com/xml/rss/all.xml', 'category' => 'middle_east', 'name' => 'Al Jazeera'],
['url' => 'https://www.jpost.com/rss/front-page', 'category' => 'middle_east', 'name' => 'Jerusalem Post'],
['url' => 'https://english.alarabiya.net/rss', 'category' => 'middle_east', 'name' => 'Al Arabiya'],
['url' => 'https://gulfnews.com/rss/news', 'category' => 'middle_east', 'name' => 'Gulf News'],

// Central Asia
['url' => 'https://astanatimes.com/feed/', 'category' => 'central_asia', 'name' => 'Astana Times'],
['url' => 'https://www.timesca.com/index.php/rss', 'category' => 'central_asia', 'name' => 'Times of Central Asia'],
['url' => 'https://eurasianet.org/rss', 'category' => 'central_asia', 'name' => 'Eurasianet'],
['url' => 'https://www.inform.kz/en/rss', 'category' => 'central_asia', 'name' => 'Kazinform'],

// Russia & CIS
['url' => 'https://tass.com/rss/v2.xml', 'category' => 'russia_news', 'name' => 'TASS'],
['url' => 'https://rg.ru/rss', 'category' => 'russia_news', 'name' => 'Rossiyskaya Gazeta'],
['url' => 'https://interfax.com/newseng.asp?y=2021', 'category' => 'russia_news', 'name' => 'Interfax'],
['url' => 'https://www.themoscowtimes.com/rss/news', 'category' => 'russia_news', 'name' => 'Moscow Times'],

// Pakistan & Bangladesh
['url' => 'https://www.dawn.com/feed', 'category' => 'south_asia', 'name' => 'Dawn'],
['url' => 'https://tribune.com.pk/feed/home', 'category' => 'south_asia', 'name' => 'Express Tribune'],
['url' => 'https://www.thedailystar.net/rss.xml', 'category' => 'south_asia', 'name' => 'Daily Star'],
['url' => 'https://bdnews24.com/rss.php', 'category' => 'south_asia', 'name' => 'bdnews24'],
// Add these to your existing getKnownFeeds() array

// Australia
['url' => 'https://www.abc.net.au/news/feed/45910/rss.xml', 'category' => 'australia_news', 'name' => 'ABC News'],
['url' => 'https://www.smh.com.au/rss/feed.xml', 'category' => 'australia_news', 'name' => 'Sydney Morning Herald'],
['url' => 'https://www.theaustralian.com.au/feed', 'category' => 'australia_news', 'name' => 'The Australian'],
['url' => 'https://www.news.com.au/feed', 'category' => 'australia_news', 'name' => 'News.com.au'],
['url' => 'https://www.theage.com.au/rss/feed.xml', 'category' => 'australia_news', 'name' => 'The Age'],

// New Zealand
['url' => 'https://www.nzherald.co.nz/rss/', 'category' => 'newzealand_news', 'name' => 'NZ Herald'],
['url' => 'https://www.stuff.co.nz/rss', 'category' => 'newzealand_news', 'name' => 'Stuff.co.nz'],
['url' => 'https://www.rnz.co.nz/rss', 'category' => 'newzealand_news', 'name' => 'Radio New Zealand'],
['url' => 'https://www.newshub.co.nz/home.rss', 'category' => 'newzealand_news', 'name' => 'Newshub'],

// Pacific Islands
['url' => 'https://www.rnz.co.nz/international/rss', 'category' => 'pacific_news', 'name' => 'RNZ Pacific'],
['url' => 'https://www.pina.com.fj/feed/', 'category' => 'pacific_news', 'name' => 'Pacific Islands News Association'],
['url' => 'https://www.samoanews.com/rss.xml', 'category' => 'pacific_news', 'name' => 'Samoa News'],
['url' => 'https://fijisun.com.fj/feed/', 'category' => 'pacific_news', 'name' => 'Fiji Sun'],

// South Africa
['url' => 'https://www.news24.com/rss', 'category' => 'south_africa', 'name' => 'News24'],
['url' => 'https://www.timeslive.co.za/rss/', 'category' => 'south_africa', 'name' => 'Times Live'],
['url' => 'https://mg.co.za/feed/', 'category' => 'south_africa', 'name' => 'Mail & Guardian'],
['url' => 'https://www.iol.co.za/rss', 'category' => 'south_africa', 'name' => 'IOL News'],

// East Africa
['url' => 'https://www.nation.co.ke/rss', 'category' => 'east_africa', 'name' => 'Daily Nation'],
['url' => 'https://www.monitor.co.ug/rss', 'category' => 'east_africa', 'name' => 'Daily Monitor'],
['url' => 'https://www.thecitizen.co.tz/feed', 'category' => 'east_africa', 'name' => 'The Citizen'],
['url' => 'https://www.theeastafrican.co.ke/rss', 'category' => 'east_africa', 'name' => 'The East African'],

// West Africa
['url' => 'https://www.premiumtimesng.com/feed', 'category' => 'west_africa', 'name' => 'Premium Times'],
['url' => 'https://www.vanguardngr.com/feed/', 'category' => 'west_africa', 'name' => 'Vanguard Nigeria'],
['url' => 'https://www.ghanaweb.com/GhanaHomePage/rss/', 'category' => 'west_africa', 'name' => 'GhanaWeb'],
['url' => 'https://www.thenewdawnliberia.com/feed/', 'category' => 'west_africa', 'name' => 'New Dawn Liberia'],

// North Africa
['url' => 'https://www.egypttoday.com/rss.aspx', 'category' => 'north_africa', 'name' => 'Egypt Today'],
['url' => 'https://www.moroccoworldnews.com/feed/', 'category' => 'north_africa', 'name' => 'Morocco World News'],
['url' => 'https://www.tap.info.tn/en/rss', 'category' => 'north_africa', 'name' => 'Tunisia TAP'],
['url' => 'https://www.libyaherald.com/feed/', 'category' => 'north_africa', 'name' => 'Libya Herald'],

// Central Africa
['url' => 'https://www.cameroonpostline.com/feed/', 'category' => 'central_africa', 'name' => 'Cameroon Post'],
['url' => 'https://www.journalducameroun.com/en/feed/', 'category' => 'central_africa', 'name' => 'Journal du Cameroun'],
['url' => 'https://www.adiac-congo.com/rss.xml', 'category' => 'central_africa', 'name' => 'Agence d\'Information d\'Afrique Centrale'],
['url' => 'https://www.digitalcongo.net/rss/', 'category' => 'central_africa', 'name' => 'Digital Congo'],

// Southern Africa
['url' => 'https://www.herald.co.zw/feed/', 'category' => 'southern_africa', 'name' => 'The Herald Zimbabwe'],
['url' => 'https://www.daily-mail.co.zm/feed/', 'category' => 'southern_africa', 'name' => 'Zambia Daily Mail'],
['url' => 'https://www.namibian.com.na/feed/', 'category' => 'southern_africa', 'name' => 'The Namibian'],
['url' => 'https://www.mmegi.bw/rss', 'category' => 'southern_africa', 'name' => 'Mmegi Online'],
// Add these to your existing getKnownFeeds() array

// Brazil
['url' => 'https://www.folha.uol.com.br/feed/rss/', 'category' => 'brazil_news', 'name' => 'Folha de São Paulo'],
['url' => 'https://www.estadao.com.br/rss/ultimas.xml', 'category' => 'brazil_news', 'name' => 'O Estado de S. Paulo'],
['url' => 'https://g1.globo.com/rss/g1/', 'category' => 'brazil_news', 'name' => 'G1 Globo'],
['url' => 'https://rss.uol.com.br/feed/noticias.xml', 'category' => 'brazil_news', 'name' => 'UOL Notícias'],
['url' => 'https://www.valor.com.br/rss', 'category' => 'brazil_news', 'name' => 'Valor Econômico'],

// Argentina
['url' => 'https://www.clarin.com/rss/', 'category' => 'argentina_news', 'name' => 'Clarín'],
['url' => 'https://www.lanacion.com.ar/arcio/rss/', 'category' => 'argentina_news', 'name' => 'La Nación'],
['url' => 'https://www.pagina12.com.ar/rss/', 'category' => 'argentina_news', 'name' => 'Página/12'],
['url' => 'https://www.infobae.com/feeds/rss/', 'category' => 'argentina_news', 'name' => 'Infobae'],
['url' => 'https://www.ambito.com/rss', 'category' => 'argentina_news', 'name' => 'Ámbito Financiero'],

// Chile
['url' => 'https://www.emol.com/rss/', 'category' => 'chile_news', 'name' => 'El Mercurio'],
['url' => 'https://www.latercera.com/feed/', 'category' => 'chile_news', 'name' => 'La Tercera'],
['url' => 'https://www.cooperativa.cl/rss/', 'category' => 'chile_news', 'name' => 'Radio Cooperativa'],
['url' => 'https://www.elmostrador.cl/feed/', 'category' => 'chile_news', 'name' => 'El Mostrador'],

// Colombia
['url' => 'https://www.eltiempo.com/rss/', 'category' => 'colombia_news', 'name' => 'El Tiempo'],
['url' => 'https://www.elespectador.com/feed/', 'category' => 'colombia_news', 'name' => 'El Espectador'],
['url' => 'https://www.semana.com/feed/', 'category' => 'colombia_news', 'name' => 'Semana'],
['url' => 'https://www.portafolio.co/rss', 'category' => 'colombia_news', 'name' => 'Portafolio'],

// Peru
['url' => 'https://elcomercio.pe/feed/', 'category' => 'peru_news', 'name' => 'El Comercio'],
['url' => 'https://larepublica.pe/feed/', 'category' => 'peru_news', 'name' => 'La República'],
['url' => 'https://peru21.pe/feed/', 'category' => 'peru_news', 'name' => 'Perú21'],
['url' => 'https://gestion.pe/feed/', 'category' => 'peru_news', 'name' => 'Gestión'],

// Venezuela
['url' => 'https://www.elnacional.com/feed/', 'category' => 'venezuela_news', 'name' => 'El Nacional'],
['url' => 'https://www.eluniversal.com/rss/', 'category' => 'venezuela_news', 'name' => 'El Universal'],
['url' => 'https://www.tal.cual.com.ve/feed/', 'category' => 'venezuela_news', 'name' => 'TalCual'],
['url' => 'https://efectococuyo.com/feed/', 'category' => 'venezuela_news', 'name' => 'Efecto Cocuyo'],

// Ecuador
['url' => 'https://www.eluniverso.com/feed/', 'category' => 'ecuador_news', 'name' => 'El Universo'],
['url' => 'https://www.elcomercio.com/feed/', 'category' => 'ecuador_news', 'name' => 'El Comercio EC'],
['url' => 'https://www.expreso.ec/feed/', 'category' => 'ecuador_news', 'name' => 'Expreso'],
['url' => 'https://www.eltelegrafo.com.ec/rss/', 'category' => 'ecuador_news', 'name' => 'El Telégrafo'],

// Bolivia
['url' => 'https://www.la-razon.com/rss/', 'category' => 'bolivia_news', 'name' => 'La Razón'],
['url' => 'https://www.lostiempos.com/rss', 'category' => 'bolivia_news', 'name' => 'Los Tiempos'],
['url' => 'https://eldeber.com.bo/rss/', 'category' => 'bolivia_news', 'name' => 'El Deber'],
['url' => 'https://www.paginasiete.bo/rss/', 'category' => 'bolivia_news', 'name' => 'Página Siete'],

// Paraguay
['url' => 'https://www.abc.com.py/feed/', 'category' => 'paraguay_news', 'name' => 'ABC Color'],
['url' => 'https://www.ultimahora.com/rss/', 'category' => 'paraguay_news', 'name' => 'Última Hora'],
['url' => 'https://www.lanacion.com.py/feed/', 'category' => 'paraguay_news', 'name' => 'La Nación PY'],
['url' => 'https://www.hoy.com.py/rss', 'category' => 'paraguay_news', 'name' => 'HOY'],

// Uruguay
['url' => 'https://www.elpais.com.uy/rss/', 'category' => 'uruguay_news', 'name' => 'El País UY'],
['url' => 'https://www.elobservador.com.uy/rss/', 'category' => 'uruguay_news', 'name' => 'El Observador'],
['url' => 'https://www.republica.com.uy/feed/', 'category' => 'uruguay_news', 'name' => 'La República'],
['url' => 'https://www.montevideo.com.uy/rss/', 'category' => 'uruguay_news', 'name' => 'Montevideo Portal'],

// Guyana & Suriname
['url' => 'https://www.stabroeknews.com/feed/', 'category' => 'guyana_news', 'name' => 'Stabroek News'],
['url' => 'https://demerarawaves.com/feed/', 'category' => 'guyana_news', 'name' => 'Demerara Waves'],
['url' => 'https://www.dwtonline.com/rss/', 'category' => 'suriname_news', 'name' => 'De Ware Tijd'],
['url' => 'https://www.starnieuws.com/rss/', 'category' => 'suriname_news', 'name' => 'Star Nieuws'],

// Regional News
['url' => 'https://www.mercopress.com/rss/', 'category' => 'south_america_regional', 'name' => 'MercoPress'],
['url' => 'https://latinamericareports.com/feed/', 'category' => 'south_america_regional', 'name' => 'Latin America Reports'],
['url' => 'https://nacla.org/rss.xml', 'category' => 'south_america_regional', 'name' => 'NACLA'],
['url' => 'https://www.americasquarterly.org/feed/', 'category' => 'south_america_regional', 'name' => 'Americas Quarterly'],

// Business & Economy
['url' => 'https://riotimesonline.com/feed/', 'category' => 'south_america_business', 'name' => 'The Rio Times'],
['url' => 'https://www.bnamericas.com/rss/news', 'category' => 'south_america_business', 'name' => 'BNamericas'],
['url' => 'https://www.americaeconomia.com/rss', 'category' => 'south_america_business', 'name' => 'América Economía'],
['url' => 'https://www.latameconomy.org/en/rss', 'category' => 'south_america_business', 'name' => 'LATAM Economy'],
// Add these to your existing getKnownFeeds() array

// Australian National
['url' => 'https://www.abc.net.au/news/feed/45910/rss.xml', 'category' => 'australia_national', 'name' => 'ABC News'],
['url' => 'https://www.sbs.com.au/news/feed', 'category' => 'australia_national', 'name' => 'SBS News'],
['url' => 'https://www.theaustralian.com.au/feed', 'category' => 'australia_national', 'name' => 'The Australian'],
['url' => 'https://www.theguardian.com/australia-news/rss', 'category' => 'australia_national', 'name' => 'Guardian Australia'],

// New South Wales
['url' => 'https://www.smh.com.au/rss/feed.xml', 'category' => 'nsw_news', 'name' => 'Sydney Morning Herald'],
['url' => 'https://www.dailytelegraph.com.au/feed', 'category' => 'nsw_news', 'name' => 'Daily Telegraph'],
['url' => 'https://www.newcastleherald.com.au/feed/', 'category' => 'nsw_news', 'name' => 'Newcastle Herald'],
['url' => 'https://www.illawarramercury.com.au/feed/', 'category' => 'nsw_news', 'name' => 'Illawarra Mercury'],

// Victoria
['url' => 'https://www.theage.com.au/rss/feed.xml', 'category' => 'victoria_news', 'name' => 'The Age'],
['url' => 'https://www.heraldsun.com.au/feed', 'category' => 'victoria_news', 'name' => 'Herald Sun'],
['url' => 'https://www.geelongadvertiser.com.au/feed', 'category' => 'victoria_news', 'name' => 'Geelong Advertiser'],
['url' => 'https://www.bendigoadvertiser.com.au/feed/', 'category' => 'victoria_news', 'name' => 'Bendigo Advertiser'],

// Queensland
['url' => 'https://www.couriermail.com.au/feed', 'category' => 'qld_news', 'name' => 'Courier Mail'],
['url' => 'https://www.brisbanetimes.com.au/rss/feed.xml', 'category' => 'qld_news', 'name' => 'Brisbane Times'],
['url' => 'https://www.goldcoastbulletin.com.au/feed', 'category' => 'qld_news', 'name' => 'Gold Coast Bulletin'],
['url' => 'https://www.cairnspost.com.au/feed', 'category' => 'qld_news', 'name' => 'Cairns Post'],

// Western Australia
['url' => 'https://thewest.com.au/feed', 'category' => 'wa_news', 'name' => 'The West Australian'],
['url' => 'https://www.perthnow.com.au/feed', 'category' => 'wa_news', 'name' => 'Perth Now'],
['url' => 'https://www.watoday.com.au/rss/feed.xml', 'category' => 'wa_news', 'name' => 'WA Today'],
['url' => 'https://www.albanyadvertiser.com.au/feed', 'category' => 'wa_news', 'name' => 'Albany Advertiser'],

// South Australia
['url' => 'https://www.adelaidenow.com.au/feed', 'category' => 'sa_news', 'name' => 'The Advertiser'],
['url' => 'https://indaily.com.au/feed/', 'category' => 'sa_news', 'name' => 'InDaily'],
['url' => 'https://www.portlincolntimes.com.au/feed/', 'category' => 'sa_news', 'name' => 'Port Lincoln Times'],
['url' => 'https://www.victorharbortimes.com.au/feed/', 'category' => 'sa_news', 'name' => 'Victor Harbor Times'],

// Tasmania
['url' => 'https://www.themercury.com.au/feed', 'category' => 'tas_news', 'name' => 'The Mercury'],
['url' => 'https://www.examiner.com.au/feed/', 'category' => 'tas_news', 'name' => 'The Examiner'],
['url' => 'https://www.theadvocate.com.au/feed/', 'category' => 'tas_news', 'name' => 'The Advocate'],
['url' => 'https://www.burnieadvocate.com.au/feed/', 'category' => 'tas_news', 'name' => 'Burnie Advocate'],

// Northern Territory
['url' => 'https://www.ntnews.com.au/feed', 'category' => 'nt_news', 'name' => 'NT News'],
['url' => 'https://katherinetime.com.au/feed/', 'category' => 'nt_news', 'name' => 'Katherine Times'],
['url' => 'https://www.centralianadvocate.com.au/feed', 'category' => 'nt_news', 'name' => 'Centralian Advocate'],
['url' => 'https://www.alicespringsnews.com.au/feed/', 'category' => 'nt_news', 'name' => 'Alice Springs News'],

// Antarctic Research
['url' => 'https://www.antarctica.gov.au/feed/', 'category' => 'antarctica_research', 'name' => 'Australian Antarctic Program'],
['url' => 'https://www.bas.ac.uk/feed/', 'category' => 'antarctica_research', 'name' => 'British Antarctic Survey'],
['url' => 'https://www.antarcticanz.govt.nz/feed', 'category' => 'antarctica_research', 'name' => 'Antarctica New Zealand'],
['url' => 'https://www.comnap.aq/feed/', 'category' => 'antarctica_research', 'name' => 'COMNAP'],

// Antarctic Science
['url' => 'https://www.polarjournal.ch/en/feed/', 'category' => 'antarctica_science', 'name' => 'Polar Journal'],
['url' => 'https://www.antarcticscience.com/feed/', 'category' => 'antarctica_science', 'name' => 'Antarctic Science'],
['url' => 'https://www.polarresearch.net/feed', 'category' => 'antarctica_science', 'name' => 'Polar Research'],
['url' => 'https://www.nature.com/subjects/antarctica.rss', 'category' => 'antarctica_science', 'name' => 'Nature Antarctica'],

// Southern Hemisphere News
['url' => 'https://www.southernhemisphereweather.com/feed/', 'category' => 'southern_hemisphere', 'name' => 'Southern Weather'],
['url' => 'https://www.weatherzone.com.au/feed/', 'category' => 'southern_hemisphere', 'name' => 'Weatherzone'],
['url' => 'https://www.niwa.co.nz/news/rss', 'category' => 'southern_hemisphere', 'name' => 'NIWA'],
['url' => 'https://www.bom.gov.au/rss/', 'category' => 'southern_hemisphere', 'name' => 'Bureau of Meteorology'],

// Northern Hemisphere News
['url' => 'https://www.arctic.noaa.gov/feed', 'category' => 'northern_hemisphere', 'name' => 'NOAA Arctic'],
['url' => 'https://www.climate.gov/news-features/feed', 'category' => 'northern_hemisphere', 'name' => 'Climate.gov'],
['url' => 'https://www.north.org/feed/', 'category' => 'northern_hemisphere', 'name' => 'Northern News'],
['url' => 'https://www.weathernorth.com/feed/', 'category' => 'northern_hemisphere', 'name' => 'Weather North'],

// Pacific Rim
['url' => 'https://www.pacificrim.news/feed/', 'category' => 'pacific_rim', 'name' => 'Pacific Rim News'],
['url' => 'https://asiapacificreport.nz/feed/', 'category' => 'pacific_rim', 'name' => 'Asia Pacific Report'],
['url' => 'https://www.eastasiaforum.org/feed/', 'category' => 'pacific_rim', 'name' => 'East Asia Forum'],
['url' => 'https://www.pacificnewscenter.com/feed/', 'category' => 'pacific_rim', 'name' => 'Pacific News Center'],
// Add these to your existing getKnownFeeds() array

// Washington DC
['url' => 'https://www.washingtonian.com/feed/', 'category' => 'dc_news', 'name' => 'Washingtonian'],
['url' => 'https://dcist.com/feed/', 'category' => 'dc_news', 'name' => 'DCist'],
['url' => 'https://www.popville.com/feed/', 'category' => 'dc_news', 'name' => 'PoPville'],
['url' => 'https://www.washingtonpost.com/local/feed/', 'category' => 'dc_news', 'name' => 'Washington Post Local'],
['url' => 'https://wamu.org/feed', 'category' => 'dc_news', 'name' => 'WAMU 88.5'],

// Sacramento (CA Capital)
['url' => 'https://www.sacbee.com/feed', 'category' => 'sacramento_news', 'name' => 'Sacramento Bee'],
['url' => 'https://www.capradio.org/feed/', 'category' => 'sacramento_news', 'name' => 'Capital Public Radio'],
['url' => 'https://www.abc10.com/feeds/rss.xml', 'category' => 'sacramento_news', 'name' => 'ABC10'],
['url' => 'https://www.newsreview.com/sacramento/feed/', 'category' => 'sacramento_news', 'name' => 'Sacramento News & Review'],

// Albany (NY Capital)
['url' => 'https://www.timesunion.com/feed/', 'category' => 'albany_news', 'name' => 'Times Union'],
['url' => 'https://www.news10.com/feed/', 'category' => 'albany_news', 'name' => 'NEWS10 ABC'],
['url' => 'https://www.wamc.org/feed/', 'category' => 'albany_news', 'name' => 'WAMC'],
['url' => 'https://dailygazette.com/feed/', 'category' => 'albany_news', 'name' => 'The Daily Gazette'],

// Austin (TX Capital)
['url' => 'https://www.statesman.com/feed/', 'category' => 'austin_news', 'name' => 'Austin American-Statesman'],
['url' => 'https://www.kut.org/rss', 'category' => 'austin_news', 'name' => 'KUT Austin'],
['url' => 'https://www.austinchronicle.com/feed/', 'category' => 'austin_news', 'name' => 'Austin Chronicle'],
['url' => 'https://austonia.com/feed', 'category' => 'austin_news', 'name' => 'Austonia'],

// Boston (MA Capital)
['url' => 'https://www.bostonglobe.com/rss/metro', 'category' => 'boston_news', 'name' => 'Boston Globe Metro'],
['url' => 'https://www.wbur.org/feed', 'category' => 'boston_news', 'name' => 'WBUR'],
['url' => 'https://www.universalhub.com/feed', 'category' => 'boston_news', 'name' => 'Universal Hub'],
['url' => 'https://www.bostonherald.com/feed/', 'category' => 'boston_news', 'name' => 'Boston Herald'],

// Atlanta (GA Capital)
['url' => 'https://www.ajc.com/feed', 'category' => 'atlanta_news', 'name' => 'Atlanta Journal-Constitution'],
['url' => 'https://www.wabe.org/feed/', 'category' => 'atlanta_news', 'name' => 'WABE'],
['url' => 'https://www.atlantamagazine.com/feed/', 'category' => 'atlanta_news', 'name' => 'Atlanta Magazine'],
['url' => 'https://saportareport.com/feed/', 'category' => 'atlanta_news', 'name' => 'Saporta Report'],

// Denver (CO Capital)
['url' => 'https://www.denverpost.com/feed/', 'category' => 'denver_news', 'name' => 'Denver Post'],
['url' => 'https://www.cpr.org/feed/', 'category' => 'denver_news', 'name' => 'Colorado Public Radio'],
['url' => 'https://denverite.com/feed/', 'category' => 'denver_news', 'name' => 'Denverite'],
['url' => 'https://www.westword.com/feed', 'category' => 'denver_news', 'name' => 'Westword'],

// Phoenix (AZ Capital)
['url' => 'https://www.azcentral.com/feed/', 'category' => 'phoenix_news', 'name' => 'Arizona Republic'],
['url' => 'https://kjzz.org/feed/', 'category' => 'phoenix_news', 'name' => 'KJZZ'],
['url' => 'https://www.phoenixnewtimes.com/feed', 'category' => 'phoenix_news', 'name' => 'Phoenix New Times'],
['url' => 'https://frontdoorsmedia.com/feed/', 'category' => 'phoenix_news', 'name' => 'Front Doors News'],

// Lansing (MI Capital)
['url' => 'https://www.lansingstatejournal.com/feed/', 'category' => 'lansing_news', 'name' => 'Lansing State Journal'],
['url' => 'https://www.wkar.org/feed/', 'category' => 'lansing_news', 'name' => 'WKAR'],
['url' => 'https://www.wilx.com/rss/', 'category' => 'lansing_news', 'name' => 'WILX News'],
['url' => 'https://www.cityoflansingmi.com/rss.aspx', 'category' => 'lansing_news', 'name' => 'City of Lansing'],

// Madison (WI Capital)
['url' => 'https://madison.com/news/local/feed/', 'category' => 'madison_news', 'name' => 'Wisconsin State Journal'],
['url' => 'https://www.wpr.org/rss', 'category' => 'madison_news', 'name' => 'Wisconsin Public Radio'],
['url' => 'https://isthmus.com/feed/', 'category' => 'madison_news', 'name' => 'Isthmus'],
['url' => 'https://www.channel3000.com/feed/', 'category' => 'madison_news', 'name' => 'Channel 3000'],

// Columbus (OH Capital)
['url' => 'https://www.dispatch.com/feed/', 'category' => 'columbus_news', 'name' => 'Columbus Dispatch'],
['url' => 'https://www.wosu.org/feed/', 'category' => 'columbus_news', 'name' => 'WOSU'],
['url' => 'https://www.columbusunderground.com/feed/', 'category' => 'columbus_news', 'name' => 'Columbus Underground'],
['url' => 'https://614now.com/feed/', 'category' => 'columbus_news', 'name' => '614NOW'],

// Salem (OR Capital)
['url' => 'https://www.statesmanjournal.com/feed/', 'category' => 'salem_news', 'name' => 'Statesman Journal'],
['url' => 'https://www.salemreporter.com/feed/', 'category' => 'salem_news', 'name' => 'Salem Reporter'],
['url' => 'https://www.opb.org/rss/regions/salem/', 'category' => 'salem_news', 'name' => 'OPB Salem'],
['url' => 'https://www.salemnews.com/feed/', 'category' => 'salem_news', 'name' => 'Salem News'],

// Harrisburg (PA Capital)
['url' => 'https://www.pennlive.com/arc/outboundfeeds/rss/?outputType=xml', 'category' => 'harrisburg_news', 'name' => 'PennLive'],
['url' => 'https://www.witf.org/feed/', 'category' => 'harrisburg_news', 'name' => 'WITF'],
['url' => 'https://www.abc27.com/feed/', 'category' => 'harrisburg_news', 'name' => 'ABC27'],
['url' => 'https://www.TheBurg.com/feed/', 'category' => 'harrisburg_news', 'name' => 'TheBurg'],
// New York City
['url' => 'https://www.nytimes.com/services/xml/rss/nyt/NYRegion.xml', 'category' => 'nyc_news', 'name' => 'NY Times Metro'],
['url' => 'https://gothamist.com/feed', 'category' => 'nyc_news', 'name' => 'Gothamist'],
['url' => 'https://www.thecity.nyc/rss/', 'category' => 'nyc_news', 'name' => 'The City'],
['url' => 'https://www.amny.com/feed/', 'category' => 'nyc_news', 'name' => 'amNY'],
['url' => 'https://www.villagevoice.com/feed/', 'category' => 'nyc_news', 'name' => 'Village Voice'],

// Los Angeles
['url' => 'https://www.latimes.com/local/rss2.0.xml', 'category' => 'la_news', 'name' => 'LA Times Local'],
['url' => 'https://laist.com/feed', 'category' => 'la_news', 'name' => 'LAist'],
['url' => 'https://www.lamag.com/feed/', 'category' => 'la_news', 'name' => 'Los Angeles Magazine'],
['url' => 'https://www.laweekly.com/feed/', 'category' => 'la_news', 'name' => 'LA Weekly'],

// Chicago
['url' => 'https://www.chicagotribune.com/arcio/rss/category/news/', 'category' => 'chicago_news', 'name' => 'Chicago Tribune'],
['url' => 'https://blockclubchicago.org/feed/', 'category' => 'chicago_news', 'name' => 'Block Club Chicago'],
['url' => 'https://www.wbez.org/feed', 'category' => 'chicago_news', 'name' => 'WBEZ'],
['url' => 'https://chicago.suntimes.com/feed', 'category' => 'chicago_news', 'name' => 'Chicago Sun-Times'],

// San Francisco
['url' => 'https://www.sfchronicle.com/feed/local', 'category' => 'sf_news', 'name' => 'SF Chronicle'],
['url' => 'https://www.sfgate.com/feed/rss/', 'category' => 'sf_news', 'name' => 'SFGate'],
['url' => 'https://missionlocal.org/feed/', 'category' => 'sf_news', 'name' => 'Mission Local'],
['url' => 'https://www.sfweekly.com/feed/', 'category' => 'sf_news', 'name' => 'SF Weekly'],

// Houston
['url' => 'https://www.houstonchronicle.com/feed/local', 'category' => 'houston_news', 'name' => 'Houston Chronicle'],
['url' => 'https://www.houstonpublicmedia.org/feed/', 'category' => 'houston_news', 'name' => 'Houston Public Media'],
['url' => 'https://www.houstonia.com/feed/', 'category' => 'houston_news', 'name' => 'Houstonia'],
['url' => 'https://www.houstonpress.com/feed', 'category' => 'houston_news', 'name' => 'Houston Press'],

// Philadelphia
['url' => 'https://www.inquirer.com/feed', 'category' => 'philly_news', 'name' => 'Philadelphia Inquirer'],
['url' => 'https://billypenn.com/feed/', 'category' => 'philly_news', 'name' => 'Billy Penn'],
['url' => 'https://whyy.org/feed/', 'category' => 'philly_news', 'name' => 'WHYY'],
['url' => 'https://www.phillyvoice.com/rss/', 'category' => 'philly_news', 'name' => 'PhillyVoice'],

// Miami
['url' => 'https://www.miamiherald.com/news/local/feed/', 'category' => 'miami_news', 'name' => 'Miami Herald'],
['url' => 'https://www.wlrn.org/rss', 'category' => 'miami_news', 'name' => 'WLRN'],
['url' => 'https://www.miaminewtimes.com/feed', 'category' => 'miami_news', 'name' => 'Miami New Times'],
['url' => 'https://www.thenextmiami.com/feed/', 'category' => 'miami_news', 'name' => 'The Next Miami'],

// Seattle
['url' => 'https://www.seattletimes.com/feed/', 'category' => 'seattle_news', 'name' => 'Seattle Times'],
['url' => 'https://www.thestranger.com/feed', 'category' => 'seattle_news', 'name' => 'The Stranger'],
['url' => 'https://www.kuow.org/rss', 'category' => 'seattle_news', 'name' => 'KUOW'],
// Add these to your existing getKnownFeeds() array

// Ivy League News
['url' => 'https://news.harvard.edu/gazette/feed/', 'category' => 'ivy_news', 'name' => 'Harvard Gazette'],
['url' => 'https://yaledailynews.com/feed/', 'category' => 'ivy_news', 'name' => 'Yale Daily News'],
['url' => 'https://www.dailyprincetonian.com/feed', 'category' => 'ivy_news', 'name' => 'Daily Princetonian'],
['url' => 'https://cornellsun.com/feed/', 'category' => 'ivy_news', 'name' => 'Cornell Daily Sun'],
['url' => 'https://www.columbiaspectator.com/feed/', 'category' => 'ivy_news', 'name' => 'Columbia Spectator'],

// Big Ten Universities
['url' => 'https://www.michigandaily.com/feed/', 'category' => 'big_ten_news', 'name' => 'Michigan Daily'],
['url' => 'https://www.thelantern.com/feed/', 'category' => 'big_ten_news', 'name' => 'The Lantern (OSU)'],
['url' => 'https://www.dailyillini.com/feed/', 'category' => 'big_ten_news', 'name' => 'Daily Illini'],
['url' => 'https://www.wisconsinstatejournalnews.com/feed/', 'category' => 'big_ten_news', 'name' => 'Wisconsin State Journal'],
['url' => 'https://www.mndaily.com/feed', 'category' => 'big_ten_news', 'name' => 'Minnesota Daily'],

// Top Public Universities
['url' => 'https://dailybruin.com/feed', 'category' => 'public_uni_news', 'name' => 'UCLA Daily Bruin'],
['url' => 'https://www.dailycal.org/feed/', 'category' => 'public_uni_news', 'name' => 'UC Berkeley Daily Cal'],
['url' => 'https://www.dailytexanonline.com/feed/', 'category' => 'public_uni_news', 'name' => 'Daily Texan'],
['url' => 'https://www.cavalierdaily.com/feed', 'category' => 'public_uni_news', 'name' => 'UVA Cavalier Daily'],
['url' => 'https://www.michigandaily.com/feed/', 'category' => 'public_uni_news', 'name' => 'Michigan Daily'],

// Liberal Arts Colleges
['url' => 'https://www.amherststudent.com/feed/', 'category' => 'liberal_arts_news', 'name' => 'Amherst Student'],
['url' => 'https://www.williamsrecord.com/feed/', 'category' => 'liberal_arts_news', 'name' => 'Williams Record'],
['url' => 'https://www.middleburycampus.com/feed/', 'category' => 'liberal_arts_news', 'name' => 'Middlebury Campus'],
['url' => 'https://www.bowdoinorient.com/feed/', 'category' => 'liberal_arts_news', 'name' => 'Bowdoin Orient'],
['url' => 'https://www.swarthmorephoenix.com/feed/', 'category' => 'liberal_arts_news', 'name' => 'Swarthmore Phoenix'],

// Engineering Schools
['url' => 'https://thetech.com/feed/', 'category' => 'engineering_news', 'name' => 'MIT Tech'],
['url' => 'https://caltechnews.caltech.edu/feed', 'category' => 'engineering_news', 'name' => 'Caltech News'],
['url' => 'https://engineering.stanford.edu/news/feed', 'category' => 'engineering_news', 'name' => 'Stanford Engineering'],
['url' => 'https://www.georgiatechtimes.com/feed/', 'category' => 'engineering_news', 'name' => 'Georgia Tech Times'],
['url' => 'https://www.purdue.edu/newsroom/rss/engineering.xml', 'category' => 'engineering_news', 'name' => 'Purdue Engineering'],

// Business Schools
['url' => 'https://www.hbs.edu/news/rss/', 'category' => 'business_school_news', 'name' => 'Harvard Business School'],
['url' => 'https://www.gsb.stanford.edu/insights/feed', 'category' => 'business_school_news', 'name' => 'Stanford GSB'],
['url' => 'https://www.wharton.upenn.edu/feed/', 'category' => 'business_school_news', 'name' => 'Wharton News'],
['url' => 'https://mitsloan.mit.edu/feed', 'category' => 'business_school_news', 'name' => 'MIT Sloan'],
['url' => 'https://www.chicagobooth.edu/news/rss', 'category' => 'business_school_news', 'name' => 'Chicago Booth'],

// Medical Schools
['url' => 'https://hms.harvard.edu/news/feed', 'category' => 'medical_school_news', 'name' => 'Harvard Medical'],
['url' => 'https://med.stanford.edu/news.feed.xml', 'category' => 'medical_school_news', 'name' => 'Stanford Medicine'],
['url' => 'https://www.hopkinsmedicine.org/news/rss.html', 'category' => 'medical_school_news', 'name' => 'Johns Hopkins Medicine'],
['url' => 'https://www.med.upenn.edu/news/feed.html', 'category' => 'medical_school_news', 'name' => 'Penn Medicine'],
['url' => 'https://medschool.ucla.edu/news/feed', 'category' => 'medical_school_news', 'name' => 'UCLA Medical'],

// Law Schools
['url' => 'https://today.law.harvard.edu/feed/', 'category' => 'law_school_news', 'name' => 'Harvard Law'],
['url' => 'https://law.yale.edu/news/rss.xml', 'category' => 'law_school_news', 'name' => 'Yale Law'],
['url' => 'https://www.law.stanford.edu/feed', 'category' => 'law_school_news', 'name' => 'Stanford Law'],
['url' => 'https://www.law.columbia.edu/news/feed', 'category' => 'law_school_news', 'name' => 'Columbia Law'],
['url' => 'https://www.law.uchicago.edu/rss.xml', 'category' => 'law_school_news', 'name' => 'Chicago Law'],
// Add these to your existing getKnownFeeds() array

// Arts Schools
['url' => 'https://www.risd.edu/news/feed', 'category' => 'art_school_news', 'name' => 'RISD News'],
['url' => 'https://www.sva.edu/feed', 'category' => 'art_school_news', 'name' => 'School of Visual Arts'],
['url' => 'https://www.parsons.edu/news/feed/', 'category' => 'art_school_news', 'name' => 'Parsons School of Design'],
['url' => 'https://www.pratt.edu/news/feed/', 'category' => 'art_school_news', 'name' => 'Pratt Institute'],
['url' => 'https://www.mica.edu/feed/', 'category' => 'art_school_news', 'name' => 'Maryland Institute College of Art'],

// Music Schools
['url' => 'https://www.juilliard.edu/news/rss', 'category' => 'music_school_news', 'name' => 'Juilliard News'],
['url' => 'https://www.berklee.edu/news/feed', 'category' => 'music_school_news', 'name' => 'Berklee News'],
['url' => 'https://music.yale.edu/news/feed', 'category' => 'music_school_news', 'name' => 'Yale School of Music'],
['url' => 'https://www.curtis.edu/news/feed/', 'category' => 'music_school_news', 'name' => 'Curtis Institute'],
['url' => 'https://www.colburn.edu/news/feed/', 'category' => 'music_school_news', 'name' => 'Colburn School'],

// Film Schools
['url' => 'https://cinema.usc.edu/news/feed.cfm', 'category' => 'film_school_news', 'name' => 'USC Cinema'],
['url' => 'https://tisch.nyu.edu/news/feed.html', 'category' => 'film_school_news', 'name' => 'NYU Tisch'],
['url' => 'https://www.afi.com/news/rss/', 'category' => 'film_school_news', 'name' => 'AFI Conservatory'],
['url' => 'https://www.chapman.edu/dodge/news/feed.aspx', 'category' => 'film_school_news', 'name' => 'Chapman Film'],
['url' => 'https://www.ucla.edu/tft/news/feed', 'category' => 'film_school_news', 'name' => 'UCLA Film School'],

// Research Universities
['url' => 'https://news.mit.edu/rss/feed', 'category' => 'research_uni_news', 'name' => 'MIT News'],
['url' => 'https://www.caltech.edu/rss/news', 'category' => 'research_uni_news', 'name' => 'Caltech News'],
['url' => 'https://www.stanford.edu/news/feed/', 'category' => 'research_uni_news', 'name' => 'Stanford News'],
['url' => 'https://www.berkeley.edu/news/feed', 'category' => 'research_uni_news', 'name' => 'Berkeley News'],
['url' => 'https://news.uchicago.edu/rss', 'category' => 'research_uni_news', 'name' => 'UChicago News'],

// Community Colleges
['url' => 'https://www.santamonicacollegenews.com/feed/', 'category' => 'community_college_news', 'name' => 'Santa Monica College'],
['url' => 'https://news.valenciacollege.edu/feed/', 'category' => 'community_college_news', 'name' => 'Valencia College'],
['url' => 'https://www.monroecc.edu/news/feed/', 'category' => 'community_college_news', 'name' => 'Monroe Community College'],
['url' => 'https://news.austincc.edu/feed/', 'category' => 'community_college_news', 'name' => 'Austin Community College'],
['url' => 'https://www.kcc.edu/news/feed/', 'category' => 'community_college_news', 'name' => 'Kingsborough Community College'],

// Technical Institutes
['url' => 'https://www.rit.edu/news/feed', 'category' => 'tech_institute_news', 'name' => 'RIT News'],
['url' => 'https://www.wpi.edu/news/feed', 'category' => 'tech_institute_news', 'name' => 'WPI News'],
['url' => 'https://www.fit.edu/news-feed/', 'category' => 'tech_institute_news', 'name' => 'Florida Tech'],
['url' => 'https://www.msoe.edu/news/feed/', 'category' => 'tech_institute_news', 'name' => 'Milwaukee School of Engineering'],
['url' => 'https://www.stevens.edu/news/feed', 'category' => 'tech_institute_news', 'name' => 'Stevens Institute of Technology'],

// Agricultural Schools
['url' => 'https://www.agriculture.tamu.edu/news/feed/', 'category' => 'agriculture_school_news', 'name' => 'Texas A&M Agriculture'],
['url' => 'https://www.caes.uga.edu/news/feed.html', 'category' => 'agriculture_school_news', 'name' => 'UGA Agriculture'],
['url' => 'https://www.canr.msu.edu/news/feed', 'category' => 'agriculture_school_news', 'name' => 'Michigan State Agriculture'],
['url' => 'https://cals.cornell.edu/news/feed', 'category' => 'agriculture_school_news', 'name' => 'Cornell Agriculture'],
['url' => 'https://ag.purdue.edu/news/feed.xml', 'category' => 'agriculture_school_news', 'name' => 'Purdue Agriculture'],

// Military Academies
['url' => 'https://www.usma.edu/news/feed', 'category' => 'military_academy_news', 'name' => 'West Point News'],
['url' => 'https://www.usna.edu/NewsCenter/feed.xml', 'category' => 'military_academy_news', 'name' => 'Naval Academy News'],
['url' => 'https://www.usafa.edu/news/feed/', 'category' => 'military_academy_news', 'name' => 'Air Force Academy News'],
['url' => 'https://www.usmma.edu/news/feed', 'category' => 'military_academy_news', 'name' => 'Merchant Marine Academy'],
['url' => 'https://www.uscga.edu/news-feed/', 'category' => 'military_academy_news', 'name' => 'Coast Guard Academy']
    
    
  
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