<?php 
require_once __DIR__ . '/../layouts/header.php'; 

echo '<link rel="stylesheet" href="./app/assets/css/builder.css">';
?>

<main class="builder-container">
    <div class="builder-header">
        <div class="utility-icons">
            <div class="utility-item">
                <div class="icon">🛒</div>
                <span>Add to Cart</span>
            </div>
            <div class="utility-item">
                <div class="icon">💾</div>
                <span>Save PC</span>
            </div>
            <div class="utility-item">
                <div class="icon">🖨️</div>
                <span>Print</span>
            </div>
            <div class="utility-item">
                <div class="icon">📸</div>
                <span>Screenshot</span>
            </div>
        </div>
    </div>

    
    <div class="builder-title">
        <h1>PC Builder - Build Your Own Computer - TechKhor</h1>
        <div class="toggle-option">
            <input type="checkbox" id="hideUnconfigured">
            <label for="hideUnconfigured">Hide Unconfigured Components</label>
        </div>
    </div>

    
    <div class="builder-stats">
        <div class="wattage-box">
            <div class="wattage-value">0W</div>
            <div class="wattage-label">BETA Estimated Wattage</div>
        </div>
        <div class="items-button">
            <span>🛒</span>
            <span>0 Items</span>
        </div>
    </div>

    
    <div class="components-container">
        <div class="component-item">
            <div class="component-icon">💻</div>
            <div class="component-info">
                <div class="component-label">
                    <span>CPU</span>
                    <span class="required-badge">Required</span>
                </div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">❄️</div>
            <div class="component-info">
                <div class="component-label">CPU Cooler</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🎛️</div>
            <div class="component-info">
                <div class="component-label">
                    <span>Motherboard</span>
                    <span class="required-badge">Required</span>
                </div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🧠</div>
            <div class="component-info">
                <div class="component-label">
                    <span>RAM</span>
                    <span class="required-badge">Required</span>
                </div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">💾</div>
            <div class="component-info">
                <div class="component-label">
                    <span>Storage</span>
                    <span class="required-badge">Required</span>
                </div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🎮</div>
            <div class="component-info">
                <div class="component-label">Graphics Card</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🔌</div>
            <div class="component-info">
                <div class="component-label">
                    <span>Power Supply</span>
                    <span class="required-badge">Required</span>
                </div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">📦</div>
            <div class="component-info">
                <div class="component-label">Casing</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>
    </div>

   
    <div class="peripherals-header">
        <h2>Peripherals & Others</h2>
    </div>

    <div class="components-container peripherals-section">
        <div class="component-item">
            <div class="component-icon">🖥️</div>
            <div class="component-info">
                <div class="component-label">Monitor</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">❄️</div>
            <div class="component-info">
                <div class="component-label">Casing Cooler</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">⌨️</div>
            <div class="component-info">
                <div class="component-label">Keyboard</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🖱️</div>
            <div class="component-info">
                <div class="component-label">Mouse</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🔊</div>
            <div class="component-info">
                <div class="component-label">Speaker & Home Theater</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🎧</div>
            <div class="component-info">
                <div class="component-label">Headphone</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">📡</div>
            <div class="component-info">
                <div class="component-label">Wifi Adapter / LAN Card</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🛡️</div>
            <div class="component-info">
                <div class="component-label">Anti Virus</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>

        <div class="component-item">
            <div class="component-icon">🔋</div>
            <div class="component-info">
                <div class="component-label">UPS</div>
                <div class="progress-bar"></div>
            </div>
            <button class="choose-btn">Choose</button>
        </div>
    </div>
</main>


<div class="floating-actions">
    <div class="action-button compare-btn">
        <div class="button-icon">➕</div>
        <div class="button-label">COMPARE</div>
        <div class="notification-badge">0</div>
    </div>
    <div class="action-button cart-btn">
        <div class="button-icon">🛒</div>
        <div class="button-label">CART</div>
        <div class="notification-badge">0</div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>