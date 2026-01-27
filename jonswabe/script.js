// ======================
// GLOBAL VARIABLES
// ======================
let activeRequests = 0;
let maxCount = 5;
let stopRequest = false;
let pauseRequest = false;
let queue = [];
let successResults = [];
let vbvResults = [];
let deadResults = [];
let processedCount = 0;
let totalCards = 0;
let startTime = 0;
let soundEnabled = true;

// Statistics
let stats = {
    success: 0,
    vbv: 0,
    dead: 0,
    total: 0,
    avgTime: 0,
    times: []
};

// ======================
// INITIALIZATION
// ======================
function updateCurrentTime() {
    const now = new Date();
    const currentTime = now.toLocaleString("en-US");
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('current-time').innerHTML = `Current Time: ${currentTime}<br>Timezone: ${userTimezone}`;
}

setInterval(updateCurrentTime, 1000);
updateCurrentTime();

// Load saved settings
$(document).ready(function() {
    loadSettings();
    initializeEventHandlers();
});

function loadSettings() {
    const savedSound = localStorage.getItem('soundEnabled');
    if (savedSound !== null) {
        soundEnabled = savedSound === 'true';
        updateSoundIcon();
    }
    
    // Don't restore stats on page load - start fresh each time
    // Stats are only saved during active session
}

function saveSettings() {
    localStorage.setItem('soundEnabled', soundEnabled);
    // Don't save stats to localStorage - they reset on page refresh
}

// ======================
// EVENT HANDLERS
// ======================
function initializeEventHandlers() {
    // Worker slider
    $("#concurrent-bar").on("input", function() {
        maxCount = $(this).val();
        $("#concurrent-value").text(maxCount);
    });

    // Sound toggle
    $("#sound-toggle").click(function() {
        soundEnabled = !soundEnabled;
        updateSoundIcon();
        saveSettings();
        
        Swal.fire({
            title: soundEnabled ? "Sound Enabled" : "Sound Disabled",
            icon: "info",
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Show/Hide buttons
    $("#success-show-btn").click(() => toggleResultDiv("#success-div", "#success-show-btn"));
    $("#vbv-show-btn").click(() => toggleResultDiv("#vbv-div", "#vbv-show-btn"));
    $("#dead-show-btn").click(() => toggleResultDiv("#dead-div", "#dead-show-btn"));

    // Copy buttons
    $("#success-copy-btn").click(() => copyToClipboard("#success-div"));
    $("#vbv-copy-btn").click(() => copyToClipboard("#vbv-div"));

    // Export buttons
    $("#success-export-btn").click(() => exportResults('success'));
    $("#vbv-export-btn").click(() => exportResults('vbv'));

    // Clear/Retry buttons
    $("#dead-clear-btn").click(() => clearDeadResults());
    $("#dead-retry-btn").click(() => retryDeadCards());

    // Control buttons
    $("#start").click(() => startChecking());
    $("#pause").click(() => pauseChecking());
    $("#stop").click(() => stopChecking());
    $("#remove").click(() => removeDuplicates());
    $("#format-lista").click(() => formatCards());

    // File upload
    $("#fileInput").on('change', handleFileUpload);

    // Card generator
    $("#generate-cards-btn").click(() => generateCards());

    // Session management
    $("#save-session-btn").click(() => saveSession());
    $("#load-session-btn").click(() => $("#session-file-input").click());
    $("#session-file-input").on('change', loadSession);
    $("#export-all-btn").click(() => exportAllResults());
    $("#clear-all-btn").click(() => clearAllData());


    // Update card count on input
    $("#lista").on('input', updateCardCount);
}

function updateSoundIcon() {
    const icon = soundEnabled ? 'fa-volume-up' : 'fa-volume-mute';
    $("#sound-icon").removeClass('fa-volume-up fa-volume-mute').addClass(icon);
}

// ======================
// CARD MANAGEMENT
// ======================
function updateCardCount() {
    const cards = $("#lista").val().split("\n").filter(line => line.trim());
    const validCards = cards.filter(card => validateCardFormat(card));
    
    $("#card-count").text(`${cards.length} cards loaded`);
    $("#valid-count").text(`${validCards.length} valid`);
}

function validateCardFormat(card) {
    return /^\d{13,19}\|\d{1,2}\|\d{2,4}\|\d{3,4}$/.test(card.trim());
}

function formatCards() {
    const card = $("#lista").val();
    const formatted = Array.from(new Set(card.match(/\b\d{13,19}\|\d{1,2}\|\d{2,4}\|\d{3,4}\b/g) || [])).join("\n");
    $("#lista").val(formatted);
    updateCardCount();
    
    Swal.fire({
        title: "Cards Formatted!",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
}

function removeDuplicates() {
    const input = $("#lista").val();
    const lines = input.split('\n').map(line => line.trim()).filter(Boolean);
    const unique = [...new Set(lines)];
    
    $("#lista").val(unique.join('\n'));
    updateCardCount();
    
    Swal.fire({
        title: "Duplicates Removed!",
        text: `Removed ${lines.length - unique.length} duplicate(s).`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $("#lista").val(e.target.result);
            updateCardCount();
            Swal.fire({
                title: "File Uploaded!",
                text: `Loaded ${file.name}`,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });
        };
        reader.readAsText(file);
    }
}

// ======================
// CARD GENERATOR
// ======================
function generateCards() {
    const bin = $("#gen-bin").val().trim();
    const month = $("#gen-month").val().trim();
    const year = $("#gen-year").val().trim();
    const cvv = $("#gen-cvv").val().trim();
    const amount = parseInt($("#gen-amount").val()) || 10;
    
    if (!bin || bin.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid BIN',
            text: 'BIN must be at least 6 digits'
        });
        return;
    }
    
    const cards = [];
    for (let i = 0; i < amount; i++) {
        const card = generateSingleCard(bin, month, year, cvv);
        cards.push(card);
    }
    
    const currentCards = $("#lista").val().trim();
    const newCards = currentCards ? currentCards + "\n" + cards.join("\n") : cards.join("\n");
    $("#lista").val(newCards);
    updateCardCount();
    
    $('#generatorModal').modal('hide');
    
    Swal.fire({
        title: "Cards Generated!",
        text: `Generated ${amount} card(s)`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
}

function generateSingleCard(bin, month, year, cvv) {
    const cardLength = 16;
    let cardNumber = bin + randomDigits(cardLength - bin.length - 1);
    
    // Luhn algorithm
    let sum = 0;
    let isEven = false;
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber[i]);
        if (isEven) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
        isEven = !isEven;
    }
    
    const checkDigit = (10 - (sum % 10)) % 10;
    cardNumber += checkDigit;
    
    const finalMonth = month || String(Math.floor(Math.random() * 12) + 1).padStart(2, '0');
    const finalYear = year || String(2025 + Math.floor(Math.random() * 5));
    const finalCvv = cvv || String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    
    return `${cardNumber}|${finalMonth}|${finalYear}|${finalCvv}`;
}

function randomDigits(length) {
    let result = '';
    for (let i = 0; i < length; i++) {
        result += Math.floor(Math.random() * 10);
    }
    return result;
}

// ======================
// CHECKER FUNCTIONS
// ======================
function startChecking() {
    const cards = $("#lista").val().split("\n").filter(line => line.trim());
    
    if (cards.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Cards',
            text: 'Please enter at least one card.'
        });
        return;
    }
    
    // Reset state
    stopRequest = false;
    pauseRequest = false;
    successResults = [];
    vbvResults = [];
    deadResults = [];
    processedCount = 0;
    totalCards = cards.length;
    startTime = Date.now();
    queue = [...cards];
    
    // Update UI
    $("#start").prop('disabled', true);
    $("#pause").prop('disabled', false);
    $("#stop").prop('disabled', false);
    $("#checker-status").text("RUNNING").css('background', 'var(--status-success)');
    $("#queue-count").text(queue.length);
    
    Swal.fire({
        title: "Checker Started!",
        text: `Checking ${totalCards} card(s)`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
    
    // Start processing
    for (let i = 0; i < Math.min(maxCount, queue.length); i++) {
        processNext();
    }
}

function pauseChecking() {
    pauseRequest = !pauseRequest;
    
    if (pauseRequest) {
        $("#pause").html('<i class="fas fa-play"></i> RESUME');
        $("#checker-status").text("PAUSED").css('background', 'var(--status-warning)');
    } else {
        $("#pause").html('<i class="fas fa-pause"></i> PAUSE');
        $("#checker-status").text("RUNNING").css('background', 'var(--status-success)');
        
        // Resume processing
        while (activeRequests < maxCount && queue.length > 0 && !stopRequest) {
            processNext();
        }
    }
}

function stopChecking() {
    stopRequest = true;
    flushResults();
    
    $("#start").prop('disabled', false);
    $("#pause").prop('disabled', true).html('<i class="fas fa-pause"></i> PAUSE');
    $("#stop").prop('disabled', true);
    $("#checker-status").text("STOPPED").css('background', 'var(--status-declined)');
    
    Swal.fire({
        title: "Checker Stopped!",
        text: "Ongoing requests will continue.",
        icon: "info",
        timer: 2000,
        showConfirmButton: false
    });
}

function processNext() {
    if (stopRequest || pauseRequest || queue.length === 0 || activeRequests >= maxCount) {
        return;
    }
    
    const card = queue.shift();
    $("#queue-count").text(queue.length);
    $("#checking-count").text(activeRequests + 1);
    
    checkCard(card);
}

function checkCard(card) {
    activeRequests++;
    const cardStartTime = Date.now();
    
    $.ajax({
        url: 'check.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ cards: [card] }),
        timeout: 30000,
        success: function(response) {
            const cardTime = (Date.now() - cardStartTime) / 1000;
            stats.times.push(cardTime);
            
            if (response.results && response.results.length > 0) {
                handleResult(response.results[0]);
            } else {
                handleError(card, "Invalid response");
            }
        },
        error: function(xhr, status, error) {
            handleError(card, error);
        },
        complete: function() {
            activeRequests--;
            processedCount++;
            $("#checking-count").text(activeRequests);
            
            updateProgress();
            
            if (processedCount % 5 === 0) {
                flushResults();
            }
            
            if (processedCount >= totalCards) {
                finishChecking();
            } else if (!stopRequest && !pauseRequest) {
                processNext();
            }
        }
    });
}

function handleResult(result) {
    const { card, status, message, bank, country } = result;
    const bankInfo = bank || 'N/A';
    const countryInfo = country || 'N/A';
    const timestamp = new Date().toLocaleTimeString();
    
    const resultHTML = `<div>
        <span class="badge info-${status === 'success' ? 'success' : status === 'vbv_required' ? 'vbv' : 'dead'}">
            ${status.toUpperCase().replace('_', ' ')}
        </span>
        <strong>${card}</strong><br>
        <small>Message: ${message} | Bank: ${bankInfo} | Country: ${countryInfo} | Time: ${timestamp}</small>
    </div>`;
    
    if (status === 'success') {
        successResults.push(resultHTML);
        stats.success++;
        playSound('success');
    } else if (status === 'vbv_required') {
        vbvResults.push(resultHTML);
        stats.vbv++;
        playSound('vbv');
    } else {
        deadResults.push(resultHTML);
        stats.dead++;
        playSound('dead');
    }
    
    stats.total++;
    updateStatistics();
}

function handleError(card, error) {
    const timestamp = new Date().toLocaleTimeString();
    const resultHTML = `<div>
        <span class="badge info-dead">ERROR</span>
        <strong>${card}</strong><br>
        <small>Error: ${error} | Time: ${timestamp}</small>
    </div>`;
    
    deadResults.push(resultHTML);
    stats.dead++;
    stats.total++;
    playSound('dead');
    updateStatistics();
}

function flushResults() {
    if (successResults.length) {
        $("#success-div").append(successResults.join(''));
        $("#success-count").text(stats.success);
        successResults = [];
    }
    if (vbvResults.length) {
        $("#vbv-div").append(vbvResults.join(''));
        $("#vbv-count").text(stats.vbv);
        vbvResults = [];
    }
    if (deadResults.length) {
        $("#dead-div").append(deadResults.join(''));
        $("#dead-count").text(stats.dead);
        deadResults = [];
    }
}

function finishChecking() {
    flushResults();
    
    $("#start").prop('disabled', false);
    $("#pause").prop('disabled', true);
    $("#stop").prop('disabled', true);
    $("#checker-status").text("IDLE").css('background', 'var(--accent-primary)');
    
    const successRate = totalCards > 0 ? ((stats.success / totalCards) * 100).toFixed(1) : 0;
    
    Swal.fire({
        title: "Checking Complete!",
        html: `
            <div style="text-align: left;">
                <p><strong>Success:</strong> ${stats.success}</p>
                <p><strong>VBV Required:</strong> ${stats.vbv}</p>
                <p><strong>Dead:</strong> ${stats.dead}</p>
                <p><strong>Success Rate:</strong> ${successRate}%</p>
            </div>
        `,
        icon: "success"
    });
    
    saveSettings();
}

// ======================
// STATISTICS
// ======================
function updateStatistics() {
    // Statistics tracking for internal use only
    // No UI updates since dashboard was removed
}

function updateProgress() {
    const percentage = totalCards > 0 ? ((processedCount / totalCards) * 100).toFixed(0) : 0;
    $("#progress-bar").css('width', percentage + '%');
    $("#progress-text").text(percentage + '%');
}

function resetStatistics() {
    Swal.fire({
        title: 'Reset Statistics?',
        text: "This will clear all statistics data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            stats = { success: 0, vbv: 0, dead: 0, total: 0, avgTime: 0, times: [] };
            updateStatistics();
            saveSettings();
            
            Swal.fire({
                title: 'Statistics Reset!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// ======================
// UI FUNCTIONS
// ======================
function toggleResultDiv(divSelector, btnSelector) {
    const div = $(divSelector);
    const btn = $(btnSelector);
    
    if (div.is(":visible")) {
        div.fadeOut(300, function() {
            div.removeClass('show');
        });
        btn.removeClass('pressed');
    } else {
        div.fadeIn(300, function() {
            div.addClass('show');
        });
        btn.addClass('pressed');
    }
}

function copyToClipboard(selector) {
    const text = $(selector).find('div').map(function() {
        return $(this).text().replace(/\s+/g, ' ').trim();
    }).get().join("\n");
    
    const $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
    
    Swal.fire({
        title: "Copied!",
        text: "Results copied to clipboard",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
}

function clearDeadResults() {
    $("#dead-div").empty();
    stats.dead = 0;
    $("#dead-count").text("0");
    
    Swal.fire({
        title: "Dead Results Cleared!",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
    });
}

function retryDeadCards() {
    const deadCards = [];
    $("#dead-div div").each(function() {
        const text = $(this).text();
        const match = text.match(/\d{13,19}\|\d{1,2}\|\d{2,4}\|\d{3,4}/);
        if (match) {
            deadCards.push(match[0]);
        }
    });
    
    if (deadCards.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'No Dead Cards',
            text: 'No dead cards to retry'
        });
        return;
    }
    
    $("#lista").val(deadCards.join("\n"));
    updateCardCount();
    clearDeadResults();
    
    Swal.fire({
        title: "Dead Cards Loaded!",
        text: `${deadCards.length} cards ready to retry`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
}

// ======================
// EXPORT FUNCTIONS
// ======================
function exportResults(type) {
    let content = '';
    let filename = '';
    
    if (type === 'success') {
        content = $("#success-div").text();
        filename = 'vbv_success.txt';
    } else if (type === 'vbv') {
        content = $("#vbv-div").text();
        filename = 'vbv_required.txt';
    }
    
    if (!content.trim()) {
        Swal.fire({
            icon: 'info',
            title: 'No Results',
            text: 'No results to export'
        });
        return;
    }
    
    downloadFile(content, filename);
}

function exportAllResults() {
    const data = {
        success: $("#success-div").text(),
        vbv: $("#vbv-div").text(),
        dead: $("#dead-div").text(),
        statistics: stats,
        timestamp: new Date().toISOString()
    };
    
    const csv = convertToCSV(data);
    downloadFile(csv, 'vbv_all_results.csv');
}

function convertToCSV(data) {
    let csv = 'Type,Card,Details,Timestamp\n';
    
    // Parse success
    $("#success-div div").each(function() {
        const text = $(this).text().replace(/\s+/g, ' ').trim();
        csv += `SUCCESS,"${text}"\n`;
    });
    
    // Parse VBV
    $("#vbv-div div").each(function() {
        const text = $(this).text().replace(/\s+/g, ' ').trim();
        csv += `VBV,"${text}"\n`;
    });
    
    // Parse Dead
    $("#dead-div div").each(function() {
        const text = $(this).text().replace(/\s+/g, ' ').trim();
        csv += `DEAD,"${text}"\n`;
    });
    
    return csv;
}

function downloadFile(content, filename) {
    const blob = new Blob([content], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    Swal.fire({
        title: "Exported!",
        text: `File saved as ${filename}`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
}

// ======================
// SESSION MANAGEMENT
// ======================
function saveSession() {
    const session = {
        cards: $("#lista").val(),
        successResults: $("#success-div").html(),
        vbvResults: $("#vbv-div").html(),
        deadResults: $("#dead-div").html(),
        statistics: stats,
        timestamp: new Date().toISOString()
    };
    
    const json = JSON.stringify(session, null, 2);
    downloadFile(json, 'vbv_session.json');
}

function loadSession(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const session = JSON.parse(e.target.result);
                
                $("#lista").val(session.cards || '');
                $("#success-div").html(session.successResults || '');
                $("#vbv-div").html(session.vbvResults || '');
                $("#dead-div").html(session.deadResults || '');
                
                if (session.statistics) {
                    stats = session.statistics;
                    updateStatistics();
                }
                
                updateCardCount();
                
                Swal.fire({
                    title: "Session Loaded!",
                    text: `Loaded from ${session.timestamp}`,
                    icon: "success"
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Session File',
                    text: error.message
                });
            }
        };
        reader.readAsText(file);
    }
}

function clearAllData() {
    Swal.fire({
        title: 'Clear All Data?',
        text: "This will clear all cards and results!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, clear it!',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $("#lista").val('');
            $("#success-div").empty();
            $("#vbv-div").empty();
            $("#dead-div").empty();
            
            stats = { success: 0, vbv: 0, dead: 0, total: 0, avgTime: 0, times: [] };
            updateStatistics();
            updateCardCount();
            saveSettings();
            
            Swal.fire({
                title: 'All Data Cleared!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// ======================
// SOUND FUNCTIONS
// ======================
function playSound(type) {
    if (!soundEnabled) return;
    
    const sound = document.getElementById(`${type}-sound`);
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(e => console.log('Sound play failed:', e));
    }
}

// ======================
// UTILITY FUNCTIONS
// ======================
function limit(textarea, maxLines) {
    const lines = textarea.value.split("\n");
    if (lines.length > maxLines) {
        textarea.value = lines.slice(0, maxLines).join("\n");
    }
}
