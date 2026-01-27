// PM Checker JavaScript
let pmResults = {
    hq: [],
    mid: [],
    dead: []
};

let pmChecking = false;
let pmQueue = [];
let pmConcurrentWorkers = 3;

// Update PM count
function updatePMCount() {
    const textarea = document.getElementById('pm-lista');
    const domains = textarea.value.trim().split('\n').filter(line => line.trim());
    document.getElementById('pm-count').textContent = `${domains.length} domain${domains.length !== 1 ? 's' : ''} loaded`;
}

// Update PM concurrent workers
document.getElementById('pm-concurrent-bar')?.addEventListener('input', function() {
    pmConcurrentWorkers = parseInt(this.value);
    document.getElementById('pm-concurrent-value').textContent = pmConcurrentWorkers;
});

// Remove duplicates from PM list
document.getElementById('pm-remove')?.addEventListener('click', function() {
    const textarea = document.getElementById('pm-lista');
    const domains = textarea.value.trim().split('\n').filter(line => line.trim());
    const unique = [...new Set(domains)];
    textarea.value = unique.join('\n');
    updatePMCount();
    
    Swal.fire({
        icon: 'success',
        title: 'Duplicates Removed',
        text: `Removed ${domains.length - unique.length} duplicate domain(s)`,
        timer: 2000
    });
});

// Upload file for PM checker
document.getElementById('pmFileInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const textarea = document.getElementById('pm-lista');
            const content = event.target.result;
            textarea.value = content;
            updatePMCount();
            
            Swal.fire({
                icon: 'success',
                title: 'File Loaded',
                text: 'Domains loaded successfully',
                timer: 2000
            });
        };
        reader.readAsText(file);
    }
});

// Start PM checking
document.getElementById('pm-start')?.addEventListener('click', async function() {
    const textarea = document.getElementById('pm-lista');
    const domains = textarea.value.trim().split('\n').filter(line => line.trim());
    
    if (domains.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Domains',
            text: 'Please enter at least one domain'
        });
        return;
    }
    
    pmChecking = true;
    pmQueue = [...domains];
    document.getElementById('pm-checker-status').textContent = 'RUNNING';
    document.getElementById('pm-checker-status').style.background = '#28a745';
    
    // Process domains with concurrency
    const workers = [];
    for (let i = 0; i < pmConcurrentWorkers; i++) {
        workers.push(pmWorker());
    }
    
    await Promise.all(workers);
    
    pmChecking = false;
    document.getElementById('pm-checker-status').textContent = 'IDLE';
    document.getElementById('pm-checker-status').style.background = '';
    document.getElementById('pm-progress-bar').style.width = '0%';
    document.getElementById('pm-progress-text').textContent = '0%';
    
    Swal.fire({
        icon: 'success',
        title: 'Checking Complete',
        text: `HQ: ${pmResults.hq.length} | MID: ${pmResults.mid.length} | DEAD: ${pmResults.dead.length}`
    });
});

// PM Worker function
async function pmWorker() {
    while (pmQueue.length > 0 && pmChecking) {
        const domain = pmQueue.shift();
        if (!domain) continue;
        
        try {
            const response = await fetch('pmcheck.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ domains: [domain] })
            });
            
            const data = await response.json();
            
            if (data.results && data.results.length > 0) {
                const result = data.results[0];
                
                // Categorize result
                if (result.category === 'HQ') {
                    pmResults.hq.push(result);
                    updatePMDisplay('hq', result);
                } else if (result.category === 'MID') {
                    pmResults.mid.push(result);
                    updatePMDisplay('mid', result);
                } else {
                    pmResults.dead.push(result);
                    updatePMDisplay('dead', result);
                }
                
                // Update counts
                document.getElementById('pm-hq-count').textContent = pmResults.hq.length;
                document.getElementById('pm-mid-count').textContent = pmResults.mid.length;
                document.getElementById('pm-dead-count').textContent = pmResults.dead.length;
                document.getElementById('pm-hq-display').textContent = pmResults.hq.length;
                document.getElementById('pm-mid-display').textContent = pmResults.mid.length;
                document.getElementById('pm-dead-display').textContent = pmResults.dead.length;
            }
        } catch (error) {
            console.error('Error checking domain:', error);
            pmResults.dead.push({
                domain: domain,
                category: 'DEAD',
                payment_methods: [],
                error_message: error.message
            });
        }
        
        // Update progress
        const totalDomains = document.getElementById('pm-lista').value.trim().split('\n').filter(line => line.trim()).length;
        const checked = pmResults.hq.length + pmResults.mid.length + pmResults.dead.length;
        const progress = Math.round((checked / totalDomains) * 100);
        document.getElementById('pm-progress-bar').style.width = progress + '%';
        document.getElementById('pm-progress-text').textContent = progress + '%';
    }
}

// Update PM display
function updatePMDisplay(category, result) {
    const divId = `pm-${category}-div`;
    const div = document.getElementById(divId);
    
    const resultDiv = document.createElement('div');
    resultDiv.style.marginBottom = '10px';
    resultDiv.style.padding = '10px';
    resultDiv.style.background = 'rgba(255, 255, 255, 0.05)';
    resultDiv.style.borderRadius = '5px';
    resultDiv.style.borderLeft = `4px solid ${category === 'hq' ? '#28a745' : category === 'mid' ? '#ff9800' : '#dc3545'}`;
    
    let html = `<strong>${result.domain}</strong><br>`;
    html += `<small>Payment Methods: ${result.payment_methods.join(', ') || 'None'}</small><br>`;
    html += `<small>Country: ${result.geolocation || 'Unknown'}</small><br>`;
    html += `<small>Captcha: ${result.captcha ? 'YES' : 'NO'} | Cloudflare: ${result.cloudflare ? 'YES' : 'NO'}</small><br>`;
    html += `<small>Time: ${result.execution_time}s</small>`;
    
    if (result.error_message) {
        html += `<br><small style="color: #dc3545;">Error: ${result.error_message}</small>`;
    }
    
    resultDiv.innerHTML = html;
    div.appendChild(resultDiv);
}

// Stop PM checking
document.getElementById('pm-stop')?.addEventListener('click', function() {
    pmChecking = false;
    pmQueue = [];
    document.getElementById('pm-checker-status').textContent = 'IDLE';
    document.getElementById('pm-checker-status').style.background = '';
});

// Show/Hide PM results
document.getElementById('pm-hq-show-btn')?.addEventListener('click', function() {
    const div = document.getElementById('pm-hq-div');
    div.style.display = div.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('pm-mid-show-btn')?.addEventListener('click', function() {
    const div = document.getElementById('pm-mid-div');
    div.style.display = div.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('pm-dead-show-btn')?.addEventListener('click', function() {
    const div = document.getElementById('pm-dead-div');
    div.style.display = div.style.display === 'none' ? 'block' : 'none';
});

// Copy PM results
document.getElementById('pm-hq-copy-btn')?.addEventListener('click', function() {
    copyPMResults('hq');
});

document.getElementById('pm-mid-copy-btn')?.addEventListener('click', function() {
    copyPMResults('mid');
});

function copyPMResults(category) {
    const results = pmResults[category];
    if (results.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'No Results',
            text: `No ${category.toUpperCase()} results to copy`
        });
        return;
    }
    
    const text = results.map(r => {
        return `${r.domain} | ${r.payment_methods.join(', ')} | ${r.geolocation} | CAP:${r.captcha ? 'YES' : 'NO'} | CF:${r.cloudflare ? 'YES' : 'NO'}`;
    }).join('\n');
    
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: `${results.length} ${category.toUpperCase()} result(s) copied to clipboard`,
            timer: 2000
        });
    });
}

// Export PM HQ results
document.getElementById('pm-hq-export-btn')?.addEventListener('click', function() {
    if (pmResults.hq.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'No Results',
            text: 'No HQ results to export'
        });
        return;
    }
    
    const text = pmResults.hq.map(r => {
        return `${r.domain}\nPayment Methods: ${r.payment_methods.join(', ')}\nCountry: ${r.geolocation}\nCaptcha: ${r.captcha ? 'YES' : 'NO'} | Cloudflare: ${r.cloudflare ? 'YES' : 'NO'}\nTime: ${r.execution_time}s\n${'='.repeat(50)}`;
    }).join('\n\n');
    
    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pm_hq_results.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    Swal.fire({
        icon: 'success',
        title: 'Exported!',
        text: 'HQ results exported successfully',
        timer: 2000
    });
});

// Clear PM dead results
document.getElementById('pm-dead-clear-btn')?.addEventListener('click', function() {
    pmResults.dead = [];
    document.getElementById('pm-dead-div').innerHTML = '';
    document.getElementById('pm-dead-count').textContent = '0';
    document.getElementById('pm-dead-display').textContent = '0';
    
    Swal.fire({
        icon: 'success',
        title: 'Cleared!',
        text: 'Dead results cleared',
        timer: 2000
    });
});
