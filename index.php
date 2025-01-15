<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSS AI Feed System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/universal-sentence-encoder"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1d4ed8;
            --success-color: #059669;
            --error-color: #dc2626;
            --background-color: #f3f4f6;
            --panel-background: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--background-color);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .header-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px;
        }

        .panel {
            background: var(--panel-background);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .feed-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .feed-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .feed-source {
            font-size: 0.9em;
            color: var(--primary-color);
        }

        .feed-category {
            background: var(--primary-color);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }

        .chat-container {
            height: 500px;
            overflow-y: auto;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-top: 10px;
        }

        .chat-message {
            margin: 10px 0;
            padding: 12px;
            border-radius: 8px;
            max-width: 80%;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .user-message {
            background: var(--primary-color);
            color: white;
            margin-left: auto;
        }

        .ai-message {
            background: white;
            border: 1px solid #e5e7eb;
            margin-right: auto;
        }

        .message-content {
            flex: 1;
        }

        .fancy-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .fancy-button:hover { background: var(--secondary-color); }
        .fancy-button:disabled { opacity: 0.7; cursor: not-allowed; }

        .fancy-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 14px;
        }

        .fancy-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .model-status {
            padding: 10px;
            margin: 10px 0;
            background: #f3f4f6;
            border-radius: 6px;
            font-size: 14px;
        }

        .training-progress {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin: 10px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
            z-index: 1000;
        }

        @keyframes slideIn {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .status {
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
        }

        .error { background: #fee2e2; color: #dc2626; }
        .success { background: #dcfce7; color: #059669; }
        .info { background: #e1effe; color: #1e40af; }

        .embeddings-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header-panel">
            <h1><i class="fas fa-rss"></i> RSS AI Feed System</h1>
            <div>
                <button id="processButton" class="fancy-button">
                    <i class="fas fa-sync"></i> Process Feeds
                </button>
                <button id="clearButton" class="fancy-button">
                    <i class="fas fa-trash"></i> Clear
                </button>
            </div>
        </div>

        <div id="modelStatus" class="model-status">
            Loading AI Model...
            <div class="training-progress">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>

        <div class="panel" id="statsPanel">
            <h2><i class="fas fa-chart-bar"></i> Feed Statistics</h2>
            <div class="stats-grid"></div>
        </div>

        <div class="feed-grid">
            <div class="panel">
                <h2><i class="fas fa-newspaper"></i> RSS Feeds</h2>
                <div id="feedContent"></div>
            </div>
             <div class="panel">
               <iframe height="600px" width="100%" src="http://jcmc.serveminecraft.net/airssreader/rsscreator.php"></iframe>
            </div>
            <div class="panel">
                <h2><i class="fas fa-robot"></i> AI Assistant</h2>
                <div class="input-group">
                    <input type="text" id="questionInput" class="fancy-input" 
                        placeholder="Ask about the articles...">
                    <button id="askButton" class="fancy-button" disabled>
                        <i class="fas fa-paper-plane"></i> Ask AI
                    </button>
                </div>
                <div id="chatContainer" class="chat-container"></div>
            </div>
        </div>
    </div>

    <script>
        class AIFeedProcessor {
            constructor() {
                this.model = null;
                this.vectorStore = new Map();
                this.feeds = [];
                this.processing = false;
                this.modelLoaded = false;
                this.initialize();
				this.bindEvents = this.bindEvents.bind(this);
    this.bindEvents();
            }
			bindEvents = () => {
        const processButton = document.getElementById('processButton');
        const clearButton = document.getElementById('clearButton');
        const askButton = document.getElementById('askButton');
        const questionInput = document.getElementById('questionInput');

        if (processButton) {
            processButton.addEventListener('click', () => this.processAndTrainFeeds());
        }
        if (clearButton) {
            clearButton.addEventListener('click', () => this.clearAll());
        }
        if (askButton) {
            askButton.addEventListener('click', () => this.handleQuestion());
        }
        if (questionInput) {
            questionInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.handleQuestion();
            });
        }
    }
                 initializeArticleCreator() {
                const form = document.getElementById('articleCreatorForm');
                const rssLinkEl = document.createElement('div');
                rssLinkEl.id = 'rssLinkContainer';
                form.parentNode.insertBefore(rssLinkEl, form.nextSibling);

                // Add RSS Link button
                const rssButton = document.createElement('button');
                rssButton.className = 'fancy-button mt-2';
                rssButton.innerHTML = '<i class="fas fa-rss"></i> View RSS Feed';
                rssButton.addEventListener('click', () => {
                    window.open('create_rss_feed.php?get_rss=1', '_blank');
                });

                if (form) {
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        
                        const title = document.getElementById('articleTitle').value;
                        const content = document.getElementById('articleContent').value;
                        const category = document.getElementById('articleCategory').value;
                        const source = document.getElementById('articleSource').value || 'Custom Source';

                        const data = {
                            title: title,
                            content: content,
                            category: category,
                            source: source
                        };

                        console.log('Sending data:', data);

                        try {
                            const response = await fetch('create_rss_feed.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data)
                            });

                            console.log('Response status:', response.status);
                            
                            const result = await response.json();
                            console.log('Response:', result);

                            // Check response
                            if (!result.success) {
                                throw new Error(result.message || 'Unknown error');
                            }

                            // Create a new feed item
                            const newArticle = result.data;

                            // Add to existing feeds
                            this.feeds.push(newArticle);

                            // Process the new article for embeddings
                            await this.processContentBatch([newArticle], 0, 1);

                            // Display the new feeds
                            this.displayFeeds();

                            // Show success message
                            this.showStatus('Article created and processed', 'success');

                            // Add RSS link button if not already present
                            if (!document.getElementById('rssViewButton')) {
                                rssButton.id = 'rssViewButton';
                                rssLinkEl.appendChild(rssButton);
                            }

                            // Reset the form
                            form.reset();
                        } catch (error) {
                            console.error('Full error:', error);
                            this.showStatus(error.message || 'Failed to create article', 'error');
                        }
                    });
                }
            }
 async initialize() {
    try {
        // Pre-warm the model
        this.updateModelStatus('Loading AI Model...');
        const modelPromise = use.load();

        // Continue with other initialization tasks
        this.bindEvents();

        // Wait for the model to load
        this.model = await modelPromise;

        // Pre-warm the model with a sample embedding
        await this.model.embed(['warmup']);

        this.modelLoaded = true;
        this.updateModelStatus('AI Model loaded successfully', 'success');
        document.getElementById('askButton').disabled = false;
    } catch (error) {
        console.error('Error loading model:', error);
        this.updateModelStatus('Error loading AI model', 'error');
    }
}
          async processAndTrainFeeds() {
    if (this.processing || !this.modelLoaded) return;
    
    this.processing = true;
    this.updateButton(true);
    this.showStatus('Processing feeds...', 'info');

    try {
        const response = await fetch('process_feeds.php');
        
        // Debug the raw response
        const rawText = await response.text();
        console.log('Raw response:', rawText);

        let data;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            throw new Error(`Failed to parse JSON response: ${rawText.substring(0, 100)}...`);
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        if (data.error) {
            throw new Error(data.error);
        }

        this.feeds = data.feeds || [];
        this.trainingData = this.feeds.map(feed => ({
            content: `${feed.title}\n\n${feed.content}`,
            category: feed.category,
            source: feed.source
        }));

        const batchSize = 5;
        for (let i = 0; i < this.feeds.length; i += batchSize) {
            const batch = this.feeds.slice(i, i + batchSize);
            await this.processContentBatch(batch, i, this.feeds.length);
        }

        this.displayFeeds();
        if (data.stats) this.displayStats(data.stats);
        this.showStatus('Processing completed', 'success');
    } catch (error) {
        console.error('Error:', error);
        this.showStatus(error.message, 'error');
    } finally {
        this.processing = false;
        this.updateButton(false);
    }
}

async processContentBatch(batch, current, total) {
    const progress = (current / total) * 100;
    this.updateProgress(progress);

    // Process in optimized mini-batches of 5 items
    const MINI_BATCH_SIZE = 5;
    const miniBatches = [];
    
    for (let i = 0; i < batch.length; i += MINI_BATCH_SIZE) {
        miniBatches.push(batch.slice(i, i + MINI_BATCH_SIZE));
    }

    // Process mini-batches concurrently
    const promises = miniBatches.map(async (miniBatch) => {
        const texts = miniBatch.map(feed => 
            this.preprocessText(`${feed.title} ${feed.content}`)
        );

        try {
            const embedding = await this.model.embed(texts);
            const embeddingArray = await embedding.array();

            // Store results
            miniBatch.forEach((feed, index) => {
                this.vectorStore.set(feed.title, {
                    embedding: embeddingArray[index],
                    content: feed.content,
                    source: feed.source,
                    title: feed.title,
                    category: feed.category
                });
            });

            embedding.dispose();
        } catch (error) {
            console.error('Error processing mini-batch:', error);
        }
    });

    // Wait for all mini-batches to complete
    await Promise.all(promises);
}
            async handleQuestion() {
                const input = document.getElementById('questionInput');
                const question = input.value.trim();

                if (!question) {
                    this.showStatus('Please enter a question', 'error');
                    return;
                }

                if (this.vectorStore.size === 0) {
                    this.showStatus('Please process feeds first', 'error');
                    return;
                }

                this.addMessage(question, 'user');
                input.value = '';

                this.addMessage('Thinking...', 'ai', true);

                try {
                    const answer = await this.findSimilarContent(question);
                    
                    const messages = document.getElementById('chatContainer');
                    if (messages) {
                        messages.removeChild(messages.lastChild);
                    }
                    
                    this.addMessage(answer, 'ai');
                } catch (error) {
                    console.error('Error:', error);
                    const messages = document.getElementById('chatContainer');
                    if (messages) {
                        messages.removeChild(messages.lastChild);
                    }
                    this.addMessage('Sorry, I encountered an error. Please try again.', 'ai');
                }
            }

            async findSimilarContent(question) {
                try {
                    const questionEmbedding = await this.model.embed([question]);
                    const questionVector = await questionEmbedding.array();

                    const similarities = Array.from(this.vectorStore.entries())
                        .map(([key, value]) => ({
                            key,
                            similarity: this.cosineSimilarity(questionVector[0], value.embedding),
                            content: value.content,
                            source: value.source,
                            title: value.title,
                            category: value.category
                        }))
                        .sort((a, b) => b.similarity - a.similarity);

                    const topResults = similarities.slice(0, 3);
                    
                    if (topResults[0].similarity < 0.3) {
                        return "I couldn't find relevant information for that question.";
                    }

                    const best = topResults[0];
                    return `Based on ${best.source}:\n\n${best.title}\n\n${best.content}\n\nRelevance: ${(best.similarity * 100).toFixed(1)}%`;
                } catch (error) {
                    console.error('Error finding similar content:', error);
                    return "Sorry, I encountered an error processing your question.";
                }
            }

            cosineSimilarity(a, b) {
                const dotProduct = a.reduce((sum, val, i) => sum + val * b[i], 0);
                const normA = Math.sqrt(a.reduce((sum, val) => sum + val * val, 0));
                const normB = Math.sqrt(b.reduce((sum, val) => sum + val * val, 0));
                return dotProduct / (normA * normB);
            }

            preprocessText(text) {
                return text.toLowerCase()
                    .replace(/[^\w\s]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            addMessage(text, type, isThinking = false) {
                const container = document.getElementById('chatContainer');
                if (!container) return;

                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${type}-message`;

                const icon = type === 'user' ? 'user' : 'robot';
                const iconHtml = `<i class="fas fa-${icon}"></i>`;

                if (isThinking) {
                    messageDiv.innerHTML = `
                        ${iconHtml}
                        <div class="message-content">
                            <div class="spinner"></div> ${text}
                        </div>
                    `;
                } else {
                    const formattedText = text.split('\n')
                        .filter(line => line.trim())
                        .map(line => `<p>${line}</p>`)
                        .join('');

                    messageDiv.innerHTML = `
                        ${iconHtml}
                        <div class="message-content">${formattedText}</div>
                    `;
                }

                container.appendChild(messageDiv);
                container.scrollTop = container.scrollHeight;
            }

            displayFeeds() {
    const container = document.getElementById('feedContent');
    if (!container) return;

    container.innerHTML = this.feeds.map(feed => `
        <div class="feed-item">
            <div class="feed-header">
                <span class="feed-source">${feed.source}</span>
                <span class="feed-category">${feed.category || 'General'}</span>
            </div>
            <h3>${feed.title}</h3>
            <p>${feed.content}</p>
            <a href="${feed.link}" target="_blank" class="fancy-button mt-2">
              <i class="fas fa-book-reader"></i> Read More
            </a>
            <div class="feed-stats">
                <span class="stat-badge">
                    <i class="far fa-clock"></i> 
                    ${new Date(feed.pubDate).toLocaleDateString()}
                </span>
            </div>
        </div>
    `).join('');
}

            displayStats(stats) {
                const container = document.querySelector('.stats-grid');
                if (!container) return;

                const cards = [
                    { label: 'Total Articles', value: stats.total_items },
                    { label: 'Cache Hits', value: stats.cache_hits },
                    { label: 'Vector Embeddings', value: this.vectorStore.size },
                    { label: 'Last Update', value: new Date(stats.last_update).toLocaleString() }
                ];

                if (stats.feeds_processed) {
                    Object.entries(stats.feeds_processed).forEach(([source, count]) => {
                        cards.push({ label: source, value: count });
                    });
                }

                container.innerHTML = cards.map(card => `
                    <div class="stat-card">
                        <div class="stat-label">${card.label}</div>
                        <div class="stat-value">${card.value}</div>
                    </div>
                `).join('');
            }

            updateModelStatus(message, type = 'info') {
                const status = document.getElementById('modelStatus');
                if (status) {
                    status.textContent = message;
                    status.className = `model-status ${type}`;
                }
            }

            updateProgress(percent) {
                const progressBar = document.getElementById('progressBar');
                if (progressBar) {
                    progressBar.style.width = `${percent}%`;
                }
            }

            updateButton(processing) {
                const button = document.getElementById('processButton');
                if (!button) return;

                button.disabled = processing;
                button.innerHTML = processing ? 
                    '<span class="spinner"></span> Processing...' : 
                    '<i class="fas fa-sync"></i> Process Feeds';
            }

            showStatus(message, type) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    ${message}
                `;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 3000);
            }

            clearAll() {
                this.feeds = [];
                this.vectorStore.clear();
                
                ['feedContent', 'chatContainer'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = '';
                });

                const statsGrid = document.querySelector('.stats-grid');
                if (statsGrid) statsGrid.innerHTML = '';

                this.showStatus('All data cleared', 'success');
                this.updateProgress(0);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new AIFeedProcessor();
        });
    </script>
</body>
</html>