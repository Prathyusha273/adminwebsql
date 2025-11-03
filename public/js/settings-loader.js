/**
 * Settings Loader - SQL Database Version
 * Replaces Firebase realtime listeners with SQL API calls
 */

// Global settings variables
window.settings = {
    global: {},
    distance: {},
    languages: [],
    version: {},
    map: {},
    notification: {},
    currency: {}
};

/**
 * Load all settings from SQL database
 */
async function loadAllSettingsFromSQL() {
    console.log('🔄 Loading settings from SQL database...');
    
    try {
        const response = await fetch('/api/settings/all', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success && result.data) {
            const data = result.data;
            
            // Store settings globally
            window.settings.global = data.globalSettings || {};
            window.settings.distance = data.distanceSettings || {};
            window.settings.languages = data.languages || [];
            window.settings.version = data.version || {};
            window.settings.map = data.mapSettings || {};
            window.settings.notification = data.notificationSettings || {};
            window.settings.currency = data.currency || {};
            
            console.log('✅ Settings loaded from SQL:', window.settings);
            
            // Apply settings to the page
            applySettings();
            
            return true;
        } else {
            console.error('❌ Failed to load settings:', result);
            return false;
        }
    } catch (error) {
        console.error('❌ Error loading settings from SQL:', error);
        return false;
    }
}

/**
 * Apply loaded settings to the page
 */
function applySettings() {
    // Apply logo
    if (window.settings.global.appLogo) {
        const logoElement = document.getElementById('logo_web');
        if (logoElement) {
            logoElement.src = window.settings.global.appLogo;
        }
    }

    // Apply meta title
    if (window.settings.global.meta_title) {
        if (!getCookie('meta_title')) {
            document.title = window.settings.global.meta_title;
            setCookie('meta_title', window.settings.global.meta_title, 365);
        }
    }

    // Apply distance type
    if (window.settings.distance.distanceType) {
        const distanceType = window.settings.distance.distanceType.charAt(0).toUpperCase() + 
                           window.settings.distance.distanceType.slice(1);
        const distanceTypeElement = document.getElementById('distanceType');
        if (distanceTypeElement) {
            distanceTypeElement.value = distanceType;
        }
        const globalDistanceTypeElements = document.querySelectorAll('.global_distance_type');
        globalDistanceTypeElements.forEach(el => {
            el.textContent = distanceType;
        });
    }

    // Apply languages to dropdown
    if (window.settings.languages && window.settings.languages.length > 0) {
        const languageDropdown = document.getElementById('language_dropdown');
        if (languageDropdown) {
            let activeLanguageCount = 0;
            
            window.settings.languages.forEach(lang => {
                if (lang.isActive) {
                    activeLanguageCount++;
                    const option = document.createElement('option');
                    option.value = lang.slug;
                    option.textContent = lang.title;
                    languageDropdown.appendChild(option);
                }
            });
            
            if (activeLanguageCount > 1) {
                const languageDropdownBox = document.getElementById('language_dropdown_box');
                if (languageDropdownBox) {
                    languageDropdownBox.style.visibility = 'visible';
                }
            }
        }
    }

    // Apply version
    if (window.settings.version.web_version) {
        const versionElements = document.querySelectorAll('.web_version');
        versionElements.forEach(el => {
            el.textContent = 'V:' + window.settings.version.web_version;
        });
    }

    // Store map type and Google Maps key globally
    if (window.settings.map.selectedMapType) {
        window.mapType = window.settings.map.selectedMapType === 'osm' ? 'OFFLINE' : 'ONLINE';
    } else {
        window.mapType = 'ONLINE';
    }
    
    if (window.settings.map.googleMapKey) {
        window.googleMapKey = window.settings.map.googleMapKey;
    }

    // Store notification settings
    if (window.settings.notification.serviceJson) {
        window.serviceJson = window.settings.notification.serviceJson;
    }

    // Store currency settings
    if (window.settings.currency) {
        window.currentCurrency = window.settings.currency.symbol || '₹';
        window.currencyAtRight = window.settings.currency.symbolAtRight || false;
        window.decimal_degits = window.settings.currency.decimal_degits || 2;
    }

    // Store custom ringtone
    if (window.settings.global.order_ringtone_url) {
        window.customRingtone = window.settings.global.order_ringtone_url;
    }

    console.log('✅ Settings applied to page');
}

/**
 * Cookie helper functions
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

// Load settings when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAllSettingsFromSQL);
} else {
    loadAllSettingsFromSQL();
}

