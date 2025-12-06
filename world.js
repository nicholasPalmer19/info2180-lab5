document.addEventListener('DOMContentLoaded', function() {
    const lookupButton = document.getElementById('lookup');
    const lookupCitiesButton = document.getElementById('lookupCities');
    const countryInput = document.getElementById('country');
    const resultDiv = document.getElementById('result');
    
    // Show loading indicator
    function showLoading() {
        resultDiv.innerHTML = '<div class="loading">Searching...</div>';
    }
    
    // Function to perform lookup
    function performLookup(lookupType) {
        const country = countryInput.value.trim();
        showLoading();
        
        // Create XMLHttpRequest
        const xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                console.log('AJAX Status:', xhr.status);
                console.log('Response:', xhr.responseText.substring(0, 200));
                
                if (xhr.status === 200) {
                    // Success - display results
                    if (xhr.responseText.trim() === '') {
                        resultDiv.innerHTML = '<div class="error">Server returned empty response</div>';
                    } else {
                        resultDiv.innerHTML = xhr.responseText;
                    }
                } else if (xhr.status === 404) {
                    resultDiv.innerHTML = '<div class="error">Error 404: world.php file not found</div>';
                } else if (xhr.status === 500) {
                    resultDiv.innerHTML = '<div class="error">Server error 500. Check PHP configuration.</div>';
                } else if (xhr.status === 0) {
                    resultDiv.innerHTML = '<div class="error">Cannot connect to server. Is XAMPP running?</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">Error ' + xhr.status + ': ' + xhr.statusText + '</div>';
                }
            }
        };
        
        // Handle network errors
        xhr.onerror = function() {
            resultDiv.innerHTML = '<div class="error">Network error. Check console (F12) for details.</div>';
            console.error('Network error occurred');
        };
        
        // Set timeout (10 seconds)
        xhr.timeout = 10000;
        xhr.ontimeout = function() {
            resultDiv.innerHTML = '<div class="error">Request timeout. Server is not responding.</div>';
        };
        
        // Build URL
        let url = 'world.php';
        if (country) {
            url += '?country=' + encodeURIComponent(country) + '&lookup=' + lookupType;
        } else {
            url += '?lookup=' + lookupType;
        }
        
        console.log('Requesting URL:', url);
        
        // Send request
        xhr.open('GET', url, true);
        xhr.send();
    }
    
    // Event listeners
    lookupButton.addEventListener('click', function() {
        performLookup('countries');
    });
    
    lookupCitiesButton.addEventListener('click', function() {
        performLookup('cities');
    });
    
    // Enter key support
    countryInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performLookup('countries');
        }
    });
});