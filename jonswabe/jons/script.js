if (!localStorage.getItem('loggedIn')) {
    window.location.href = 'login.html';
}

function updatePhilippineTime() {
    const now = new Date();
    const philippineTime = now.toLocaleString("en-US", {timeZone: "Asia/Manila"});
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('philippine-time').innerHTML = `Philippine Time: ${philippineTime}<br>User Timezone: ${userTimezone}`;
}

setInterval(updatePhilippineTime, 1000);
updatePhilippineTime(); // initial call

$(document).ready(function() {
    // ======================
    // CARD GENERATION FUNCTIONS
    // ======================
    $("#generate").click(function() {
        document.getElementById("lista").value = gen();
    });

    function gen() {
        var amt = $("#amount").val();
        if (amt.length == 0) {
            amt = 100;
        }
        var ccs = [];
        for (x = 0; x < amt; x++) {
            ccs.push(generate());
        }
        return ccs.join("\n");
    }

    function rand(rlength) {
        var randa = [];
        while (randa.length < rlength) {
            var rand = Math.floor(Math.random() * 10);
            randa.push(rand);
        }
        return randa.join('');
    }

    function generate() {
        var bvbin = $("#bin").val();
        var bvmonth = $("#month").val();
        var bvyear = $("#year").val();
        var bvcvv = $("#cvv").val();
        var cci = bvbin + rand(15 - bvbin.length);
        var evena = [];
        var odda = [];
        for (var e = 0; e < 15; e += 2) {
            var even = cci[e] * 2;
            if (even.toString().length == 2) {
                even = even.toString().split('');
                even = parseInt(even[0]) + parseInt(even[1]);
            }
            evena.push(even);
        }
        for (var o = 1; o < 15; o += 2) {
            var odd = cci[o];
            odda.push(odd);
        }
        var odds = eval(odda.join('+'));
        var evens = eval(evena.join('+'));
        var total = odds + evens;
        var csum = 10 - total.toString()[1];
        if (csum == 10) {
            csum = 0;
        }
        if (fyear(bvyear).length == 4) {
            return cci + csum + '|' + fmonth(bvmonth) + '|' + fyear(bvyear) + '|' + fcvv(bvcvv);
        } else {
            return cci + csum + '|' + fmonth(bvmonth) + '|202' + fyear(bvyear) + '|' + fcvv(bvcvv);
        }
    }

    function fmonth(bvmonth) {
        if (bvmonth) {
            return bvmonth;
        } else {
            var bvmonth = Math.floor(Math.random() * 12) + 1;
            if (bvmonth.toString().length == 1) {
                bvmonth = '0' + bvmonth;
            }
            return bvmonth;
        }
    }

    function fyear(bvyear) {
        if (bvyear) {
            return bvyear;
        } else {
            return Math.floor(Math.random() * 8) + 2;
        }
    }

    function fcvv(bvcvv) {
        if (bvcvv) {
            return bvcvv;
        } else {
            cvva = [];
            while (cvva.length < 3) {
                var cvv = Math.floor(Math.random() * 10);
                cvva.push(cvv);
            }
            return cvva.join('');
        }
    }

    // ======================
    // MAIN CHECKER FUNCTIONALITY
    // ======================
    let activeRequests = 0;
    let maxCount = 10;
    let stopRequest = false;
    let queue = [];
    let cvvResults = [];
    let ccnResults = [];
    let deadResults = [];
    let processedCount = 0;

    const cvvSound = new Audio('assets/sounds/cvv.mp3');
    const ccnSound = new Audio('assets/sounds/ccn.mp3');

    function flushResults() {
        if (cvvResults.length) {
            $("#cvv-div").append(cvvResults.join(''));
            cvvResults = [];
        }
        if (ccnResults.length) {
            $("#ccn-div").append(ccnResults.join(''));
            ccnResults = [];
        }
        if (deadResults.length) {
            $("#dead-div").append(deadResults.join(''));
            deadResults = [];
        }
    }

    $("#concurrent-bar").on("input", function() {
        maxCount = $(this).val();
        $("#concurrent-value").text(maxCount + "");
    });

    $("#config-visibility").click(function() {
        const isHidden = $("#proxy").attr("type") === "text";
        $("#proxy, #sites, #scraper").attr("type", isHidden ? "password" : "text");
        $("#config-visibility .material-symbols-outlined").text(isHidden ? "visibility_off" : "visibility");
    });

$("#cvv-show-btn").click(() => {
    const cvvDiv = $("#cvv-div");
    const cvvBtn = $("#cvv-show-btn");

    if (cvvDiv.is(":visible")) {
        cvvDiv.fadeOut(300, function() {
            cvvDiv.removeClass('show');
        });
        cvvBtn.removeClass('pressed');
    } else {
        cvvDiv.fadeIn(300, function() {
            cvvDiv.addClass('show');
        });
        cvvBtn.addClass('pressed');
    }
});

$("#ccn-show-btn").click(() => {
    const ccnDiv = $("#ccn-div");
    const ccnBtn = $("#ccn-show-btn");

    if (ccnDiv.is(":visible")) {
        ccnDiv.fadeOut(300, function() {
            ccnDiv.removeClass('show');
        });
        ccnBtn.removeClass('pressed');
    } else {
        ccnDiv.fadeIn(300, function() {
            ccnDiv.addClass('show');
        });
        ccnBtn.addClass('pressed');
    }
});

$("#dead-show-btn").click(() => {
    const deadDiv = $("#dead-div");
    const deadBtn = $("#dead-show-btn");

    if (deadDiv.is(":visible")) {
        deadDiv.fadeOut(300, function() {
            deadDiv.removeClass('show');
        });
        deadBtn.removeClass('pressed');
    } else {
        deadDiv.fadeIn(300, function() {
            deadDiv.addClass('show');
        });
        deadBtn.addClass('pressed');
    }
});

    $("#cvv-clear-btn").click(() => $(".m").remove());
    $("#ccn-clear-btn").click(() => $(".n").remove());
    $("#dead-clear-btn").click(() => $(".d").remove());

    $("#cvv-copy-btn").click(() => copyToClipboard("#cvv-div"));
    $("#ccn-copy-btn").click(() => copyToClipboard("#ccn-div"));
    $("#dead-copy-btn").click(() => copyToClipboard("#dead-div"));

    function copyToClipboard(selector) {
        var text = $(selector).find('div').map(function() {
            return $(this).text().replace(/\s+/g, ' ').trim();
        }).get().join("\n");

        var $temp = $("<textarea>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();

        Swal.fire({
            title: "Copied to Clipboard!",
            text: "Results copied.",
            icon: "success",
            customClass: {
                confirmButton: 'swal2-confirm',
                popup: 'swal2-popup'
            }
        });
    }

    $("#format-lista").click(function() {
        var card = $("#lista").val();
        $("#lista").val(
            Array.from(new Set(card.match(/\b\d{16}\|\d{2}\|\d{4}\|\d{3}\b/g) || [])).join("\n")
        );
    });

    $("#start").click(function() {
        let sitesInput = $("#sites").val().trim();
        if (!sitesInput) {
            Swal.fire({
                icon: 'error',
                title: 'Missing URL',
                text: 'Please enter a website URL before starting.',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    popup: 'swal2-popup'
                }
            });
            $("#sites").focus();
            return;
        }
        stopRequest = false;
        cvvResults = [];
        ccnResults = [];
        deadResults = [];
        processedCount = 0;
        let card = $("#lista").val().split("\n");
        let gate = $("#gates").val();
        let proxy = $("#proxy").val();
        let sites = $("#sites").val();
        let xlite = $("#xlite").val();
        let total = card.length;
        let cvv = 0,
            ccn = 0,
            dead = 0,
            remaining = total;
        Swal.fire({
            title: "Checker Started!",
            text: 'Checking ' + total + ' card/s.',
            icon: "success",
            customClass: {
                confirmButton: 'swal2-confirm',
                popup: 'swal2-popup'
            }
        });

        let index = 0;

        function processQueue() {
            if (stopRequest || index >= card.length || activeRequests >= maxCount) return;

            let value = card[index++];
            queue.push(value);

            if (activeRequests < maxCount) {
                sendRequest(queue.shift());
            }
        }

        function sendRequest(value) {
            activeRequests++;

            // Get the session UUID if it was set
            var sessionUuid = $('#start').data('session-uuid');

            $.ajax({
                url: 'api/' + gate + '?lista=' + value +
                    '&proxy=' + proxy +
                    '&sites=' + sites +
                    '&xlite=' + xlite +
                    (sessionUuid ? '&usersession=' + sessionUuid : ''),
                type: 'GET',
                async: true,
                success: function(result) {
                    processedCount++;
                    if (result.includes("#CVV")) {
                        cvvResults.push('<div class="m"><span class="badge"> CVV </span> ' + value + ' ' + result.replace("#CVV", "") + '</div>');
                        cvv++;
                        cvvSound.play();
                    } else if (result.includes("#CCN")) {
                        ccnResults.push('<div class="n"><span class="badge"> CCN </span> ' + value + ' ' + result.replace("#CCN", "") + '</div>');
                        ccn++;
                        ccnSound.play();
                    } else {
                        deadResults.push('<div class="d"><span class="badge"> DEAD </span> ' + value + ' ' + result.replace("#DEAD", "") + '</div>');
                        dead++;
                    }
                    remaining--;

                    $('#m-count').html(cvv);
                    $('#n-count').html(ccn);
                    $('#d-count').html(dead);
                    $('#c-count').html(remaining);

                    if (processedCount % 10 === 0) {
                        flushResults();
                        update();
                        progressBar();
                    }
                },
                complete: function() {
                    activeRequests--;
                    if (queue.length > 0) {
                        sendRequest(queue.shift());
                    } else {
                        processQueue();
                    }
                }
            });
        }

        for (let i = 0; i < maxCount && i < card.length; i++) {
            processQueue();
        }
    });

    $("#stop").click(function() {
        stopRequest = true;
        flushResults();

        Swal.fire({
            title: "Stop Requested!",
            text: "No further requests will be initiated, but ongoing requests will continue.",
            icon: "info",
            customClass: {
                confirmButton: 'swal2-confirm',
                popup: 'swal2-popup'
            }
        });
    });

    // ======================
    // SESSION MANAGEMENT
    // ======================
    $('#createSessionBtn').click(function() {
        handleSession('create');
    });

    $('#downloadSessionBtn').click(function() {
        handleSession('download');
    });

    $('#updateSessionBtn').click(function() {
        const uuid = $('#uuidInput').val().trim();
        if (!uuid) {
            Swal.fire('Error', 'UUID is required for update.', 'error');
            return;
        }
        $('#start').data('session-uuid', uuid);
        Swal.fire('Success', 'Session UUID set for checker: ' + uuid, 'success');
        $('#sessionModal').modal('hide');
    });

    $('#oSessionModal').click(function() {
        $('#sessionModal').modal('show');
    });

    function handleSession(action) {
        const uuid = $('#uuidInput').val().trim();
        const user = $('#userInput').val().trim();

        if (action === 'create' && !user) {
            Swal.fire('Error', 'User is required for creation.', 'error');
            return;
        }
        if ((action === 'download' || action === 'update') && !uuid) {
            Swal.fire('Error', 'UUID is required for this operation.', 'error');
            return;
        }

        $.post('c.php', { action, uuid, user }, function(response) {
            if (action === 'create') {
                const formattedResponse = response
                    .replace(/\n/g, '<br>')
                    .replace(/âœ…/g, 'âœ… ');

                $('#sessionResult').html(`
                    <div class="text-success" style="text-align: center;">
                        ${formattedResponse}
                    </div>
                `).show();

                Swal.fire({
                    title: 'Success',
                    html: `<div style="text-align: center; font-family: monospace; margin: 0 auto;">${formattedResponse}</div>`,
                    icon: 'success'
                });
            } else if (action === 'download') {
                const blob = new Blob([response], { type: 'text/plain' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `${uuid}.txt`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                Swal.fire('Success', 'File downloaded successfully!', 'success');
            }
        }).fail(function(error) {
            Swal.fire('Error', 'Operation failed: ' + (error.responseText || 'Server error'), 'error');
        });
    }
});

// ======================
// VANILLA JS FUNCTIONS
// ======================
document.addEventListener('DOMContentLoaded', function() {
    var editIcon = document.getElementById('editIcon');
    var sitesInput = document.getElementById('sites');
    var modalInput = document.getElementById('modalInput');
    var saveButton = document.getElementById('saveButton');
    var formatButton = document.getElementById('formatButton');
    var lineNumbers = document.getElementById('lineNumbers');

    function updateLineNumbers() {
        var lines = modalInput.value.split('\n').length;
        lineNumbers.innerHTML = '';
        for (var i = 1; i <= lines; i++) {
            lineNumbers.innerHTML += i + '<br>';
        }
    }

    editIcon.addEventListener('click', function() {
        var inputText = sitesInput.value;
        var urlPattern = /((http:\/\/|https:\/\/)[^\s,]+)/g;
        var matches = inputText.match(urlPattern);

        if (matches) {
            modalInput.value = matches.join('\n');
        } else {
            modalInput.value = '';
        }

        updateLineNumbers();

        var modal = new bootstrap.Modal(document.getElementById('sitesModal'));
        modal.show();
    });

    modalInput.addEventListener('input', function() {
        updateLineNumbers();
    });

    modalInput.addEventListener('scroll', function() {
        lineNumbers.scrollTop = modalInput.scrollTop;
    });

    saveButton.addEventListener('click', function() {
        var inputText = modalInput.value;
        var urls = inputText.split('\n').filter(Boolean);

        sitesInput.value = urls.join(',');
        $('#sitesModal').modal('hide');
    });

    formatButton.addEventListener('click', function() {
        var inputText = modalInput.value;
        var urlPattern = /((http:\/\/|https:\/\/)[^\s,]+)/g;
        var matches = inputText.match(urlPattern);

        if (matches) {
            var formattedUrls = matches.map(function(url) {
                return url.trim();
            });
            modalInput.value = formattedUrls.join('\n');
        } else {
            modalInput.value = '';
        }

        updateLineNumbers();
    });
});

// ======================
// UTILITY FUNCTIONS
// ======================
function progressBar() {
    var total = parseInt($('#c-count').html()) + parseInt($('#m-count').html()) + parseInt($('#n-count').html()) + parseInt($('#d-count').html());
    var remaining = parseInt($('#c-count').html());
    var percentage = ((total - remaining) / total) * 100;
    $('.progress-bar').css('width', percentage + '%');
}

function update() {
    var card = $("#lista").val().split("\n");
    card.splice(0, 1);
    $("#lista").val(card.join("\n"));
    progressBar();
}

function limit(textarea, maxLines) {
    var lines = textarea.value.split("\n");
    if (lines.length > maxLines) {
        textarea.value = lines.slice(0, maxLines).join("\n");
    }
}

function startScraper() {
    var scraper = document.getElementById("scraper").value;

    Swal.fire({
        title: "SCRAPER STARTED!",
        text: "SCRAPING MIGHT TAKE A WHILE.",
        icon: "info",
        customClass: {
            confirmButton: 'swal2-confirm',
            popup: 'swal2-popup'
        }
    });

    var url = "scraper.php?scraper=" + encodeURIComponent(scraper);

    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var ccData = xhr.responseText;
            document.getElementById("lista").value = ccData;
        }
    };
    xhr.send();
}

document.getElementById("remove").addEventListener("click", function () {
        const input = document.getElementById("lista").value;
        const lines = input.split('\n').map(line => line.trim()).filter(Boolean);

        const seenDomains = new Set();
        const seenLines = new Set();
        const output = [];

        for (const line of lines) {
            try {
                // Check if it's a URL
                const url = new URL(line);
                const domain = url.hostname;

                if (!seenDomains.has(domain)) {
                    seenDomains.add(domain);
                    output.push(line); // keep only first URL per domain
                }
            } catch (e) {
                // Not a URL — treat it as generic string (e.g., card number)
                if (!seenLines.has(line)) {
                    seenLines.add(line);
                    output.push(line); // keep only first occurrence
                }
            }
        }

        document.getElementById("lista").value = output.join('\n');
    });

// File upload functionality
document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('lista').value = e.target.result;
        };
        reader.readAsText(file);
    }
});




