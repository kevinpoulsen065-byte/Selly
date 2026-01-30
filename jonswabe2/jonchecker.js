$(document).ready(function() {
    // ======================
    // CARD GENERATION FUNCTIONS
    // ======================
    $("#generate").click(function() {
        document.getElementById("jon-lista").value = gen();
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

    const cvvSound = new Audio('jons/assets/sounds/cvv.mp3');
    const ccnSound = new Audio('jons/assets/sounds/ccn.mp3');

    function flushResults() {
        if (cvvResults.length) {
            $("#jon-cvv-div").append(cvvResults.join(''));
            cvvResults = [];
        }
        if (ccnResults.length) {
            $("#jon-ccn-div").append(ccnResults.join(''));
            ccnResults = [];
        }
        if (deadResults.length) {
            $("#jon-dead-div").append(deadResults.join(''));
            deadResults = [];
        }
    }

    $("#jon-concurrent-bar").on("input", function() {
        maxCount = $(this).val();
        $("#jon-concurrent-value").text(maxCount + "");
    });

    $("#jon-config-visibility").click(function() {
        const isHidden = $("#jon-proxy").attr("type") === "text";
        $("#jon-proxy, #jon-sites").attr("type", isHidden ? "password" : "text");
        $("#jon-config-visibility .material-symbols-outlined").text(isHidden ? "visibility_off" : "visibility");
    });

    $("#jon-cvv-show-btn").click(() => {
        const cvvDiv = $("#jon-cvv-div");
        const cvvBtn = $("#jon-cvv-show-btn");

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

    $("#jon-ccn-show-btn").click(() => {
        const ccnDiv = $("#jon-ccn-div");
        const ccnBtn = $("#jon-ccn-show-btn");

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

    $("#jon-dead-show-btn").click(() => {
        const deadDiv = $("#jon-dead-div");
        const deadBtn = $("#jon-dead-show-btn");

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

    $("#jon-cvv-clear-btn").click(() => $(".m").remove());
    $("#jon-ccn-clear-btn").click(() => $(".n").remove());
    $("#jon-dead-clear-btn").click(() => $(".d").remove());

    $("#jon-cvv-copy-btn").click(() => copyToClipboard("#jon-cvv-div"));
    $("#jon-ccn-copy-btn").click(() => copyToClipboard("#jon-ccn-div"));
    $("#jon-dead-copy-btn").click(() => copyToClipboard("#jon-dead-div"));

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

    $("#jon-format-lista").click(function() {
        var card = $("#jon-lista").val();
        $("#jon-lista").val(
            Array.from(new Set(card.match(/\b\d{16}\|\d{2}\|\d{4}\|\d{3}\b/g) || [])).join("\n")
        );
    });

    $("#jon-start").click(function() {
        let sitesInput = $("#jon-sites").val().trim();
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
            $("#jon-sites").focus();
            return;
        }
        stopRequest = false;
        cvvResults = [];
        ccnResults = [];
        deadResults = [];
        processedCount = 0;
        let card = $("#jon-lista").val().split("\n");
        let gate = $("#jon-gates").val();
        let proxy = $("#jon-proxy").val();
        let sites = $("#jon-sites").val();
        let xlite = $("#jon-xlite").val();
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
            var sessionUuid = $('#jon-start').data('session-uuid');

            $.ajax({
                url: 'jons/api/' + gate + '?lista=' + value +
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

                    $('#jon-m-count').html(cvv);
                    $('#jon-n-count').html(ccn);
                    $('#jon-d-count').html(dead);
                    $('#jon-c-count').html(remaining);

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

    $("#jon-stop").click(function() {
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
    $('#jon-createSessionBtn').click(function() {
        handleSession('create');
    });

    $('#jon-downloadSessionBtn').click(function() {
        handleSession('download');
    });

    $('#jon-updateSessionBtn').click(function() {
        const uuid = $('#jon-uuidInput').val().trim();
        if (!uuid) {
            Swal.fire('Error', 'UUID is required for update.', 'error');
            return;
        }
        $('#jon-start').data('session-uuid', uuid);
        Swal.fire('Success', 'Session UUID set for checker: ' + uuid, 'success');
        $('#jon-sessionModal').modal('hide');
    });

    $('#jon-oSessionModal').click(function() {
        $('#jon-sessionModal').modal('show');
    });

    function handleSession(action) {
        const uuid = $('#jon-uuidInput').val().trim();
        const user = $('#jon-userInput').val().trim();

        if (action === 'create' && !user) {
            Swal.fire('Error', 'User is required for creation.', 'error');
            return;
        }
        if ((action === 'download' || action === 'update') && !uuid) {
            Swal.fire('Error', 'UUID is required for this operation.', 'error');
            return;
        }

        $.post('jons/c.php', { action, uuid, user }, function(response) {
            if (action === 'create') {
                const formattedResponse = response
                    .replace(/\n/g, '<br>')
                    .replace(/âœ…/g, 'âœ… ');

                $('#jon-sessionResult').html(`
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
    var editIcon = document.getElementById('jon-editIcon');
    var sitesInput = document.getElementById('jon-sites');
    var modalInput = document.getElementById('jon-modalInput');
    var saveButton = document.getElementById('jon-saveButton');
    var formatButton = document.getElementById('jon-formatButton');
    var lineNumbers = document.getElementById('jon-lineNumbers');

    if (editIcon && sitesInput) {
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

            var modal = new bootstrap.Modal(document.getElementById('jon-sitesModal'));
            modal.show();
        });
    }

    if (modalInput) {
        modalInput.addEventListener('input', function() {
            updateLineNumbers();
        });

        modalInput.addEventListener('scroll', function() {
            lineNumbers.scrollTop = modalInput.scrollTop;
        });
    }

    if (saveButton) {
        saveButton.addEventListener('click', function() {
            var inputText = modalInput.value;
            var urls = inputText.split('\n').filter(Boolean);

            sitesInput.value = urls.join(',');
            $('#jon-sitesModal').modal('hide');
        });
    }

    if (formatButton) {
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
    }

    function updateLineNumbers() {
        if (lineNumbers && modalInput) {
            var lines = modalInput.value.split('\n').length;
            lineNumbers.innerHTML = '';
            for (var i = 1; i <= lines; i++) {
                lineNumbers.innerHTML += i + '<br>';
            }
        }
    }
});

// ======================
// UTILITY FUNCTIONS
// ======================
function progressBar() {
    var total = parseInt($('#jon-c-count').html()) + parseInt($('#jon-m-count').html()) + parseInt($('#jon-n-count').html()) + parseInt($('#jon-d-count').html());
    var remaining = parseInt($('#jon-c-count').html());
    var percentage = ((total - remaining) / total) * 100;
    $('#jon-progress-bar').css('width', percentage + '%');
}

function update() {
    var card = $("#jon-lista").val().split("\n");
    card.splice(0, 1);
    $("#jon-lista").val(card.join("\n"));
    progressBar();
}

function limit(textarea, maxLines) {
    var lines = textarea.value.split("\n");
    if (lines.length > maxLines) {
        textarea.value = lines.slice(0, maxLines).join("\n");
    }
}

document.getElementById("jon-remove").addEventListener("click", function () {
        const input = document.getElementById("jon-lista").value;
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

        document.getElementById("jon-lista").value = output.join('\n');
    });

// File upload functionality
document.getElementById('jon-fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('jon-lista').value = e.target.result;
        };
        reader.readAsText(file);
    }
});
