<!DOCTYPE html>
<html lang="id-ID" itemscope="itemscope" itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Urbansiana.com menyajikan berita informasi yang akurat dan aktual dari regional, nasional dan Internasional">
    <meta name="keywords" content="urbansiana, berita terkini, portal berita indonesia, berita viral, media online terpercaya, berita populer, berita terbaru, info harian, berita viral terbaru">
    <meta name="author" content="https://www.urbansiana.com">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Urbansiana | Portal Berita Terkini, Viral, dan Informatif">
    <meta property="og:description" content="Deskripsi singkat yang menarik perhatian.">
    <meta property="og:image" content="URL_gambar_thumbnail">
    <meta property="og:url" content="URL_halaman">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Judul Halaman">
    <meta name="twitter:description" content="Deskripsi singkat.">
    <meta name="twitter:image" content="URL_gambar_thumbnail">
    <link rel="canonical" href="URL_halaman">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="preload" href="font_anda.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <title>Judul Halaman yang Informatif dan Menggunakan Kata Kunci</title>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NewsMediaOrganization",
            "name": "Urbansiana",
            "url": "https://www.urbansiana.com",
            "logo": {
                "@type": "ImageObject",
                "url": "https://www.urbansiana.com/logo.png",
                "width": 600,
                "height": 60
            },
            "description": "Portal berita terkini yang menyajikan informasi terbaru dan terpercaya.",
            "sameAs": [
                "https://www.facebook.com/urbansiana",
                "https://www.twitter.com/urbansiana",
                "https://www.instagram.com/urbansiana"
            ],
            "potentialAction": {
                "@type": "SearchAction",
                "target": "https://www.urbansiana.com/search?q={search_term_string}",
                "query-input": "required name=search_term_string"
            },
            "publisher": {
                "@type": "NewsMediaOrganization",
                "name": "Urbansiana",
                "logo": {
                    "@type": "ImageObject",
                    "url": "https://www.urbansiana.com/logo.png",
                    "width": 600,
                    "height": 60
                }
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [{
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Beranda",
                        "item": "https://www.urbansiana.com"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "Artikel",
                        "item": "https://www.urbansiana.com/artikel"
                    }
                ]
            }
        }
    </script>

</head>

<body>
    <!-- Konten Halaman -->
    <header>
        <h1>Judul Utama Halaman</h1>
        <nav>
            <ul>
                <li><a href="/kategori-1">Kategori 1</a></li>
                <li><a href="/kategori-2">Kategori 2</a></li>
                <li><a href="/kategori-3">Kategori 3</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Subjudul</h2>
            <p>Konten pendukung di sini...</p>
            <img src="URL_gambar" alt="Deskripsi gambar yang relevan">
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Nama Situs Anda. Semua Hak Dilindungi.</p>
    </footer>
</body>
<script>
    !(function() {
        var time = new Date().getTime();
        document.body.addEventListener('mousemove', function() {
            time = new Date().getTime();
        });
        document.body.addEventListener('keypress', function() {
            time = new Date().getTime();
        });

        function refresh() {
            if (new Date().getTime() - time >= 900000) { // 15 menit
                window.location.reload(true);
            } else {
                setTimeout(refresh, 10000); // 10 detik
            }
        }
        setTimeout(refresh, 10000);
    })();

    function detectAdblock() {
        let adblockDetected = false;

        // Test 1: Invisible bait element
        const bait = document.createElement('div');
        bait.className = 'adsbox';
        bait.style.position = 'absolute';
        bait.style.left = '-9999px';
        document.body.appendChild(bait);

        if (!bait.offsetParent) {
            adblockDetected = true;
        }
        document.body.removeChild(bait);

        // Test 2: Fake ad resource request
        const fakeAdUrl = '/ads/ad-banner.js';
        let requestBlocked = false;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', fakeAdUrl, true);
        xhr.onload = function() {
            if (xhr.status === 0) {
                requestBlocked = true;
            }
            finishDetection(adblockDetected || requestBlocked);
        };
        xhr.onerror = function() {
            requestBlocked = true;
            finishDetection(adblockDetected || requestBlocked);
        };
        xhr.send();

        // Test 3: Brave Shields (blocked by built-in rules)
        const braveTest = new Promise((resolve) => {
            if (navigator.brave) {
                navigator.brave.isBrave().then((isBrave) => {
                    if (isBrave) {
                        resolve(true);
                    } else {
                        resolve(false);
                    }
                });
            } else {
                resolve(false);
            }
        });

        braveTest.then((isBrave) => {
            if (isBrave) {
                adblockDetected = true;
            }
            finishDetection(adblockDetected);
        });

        // Test 4: DNS Blocking (AdGuard)
        const dnsTest = document.createElement('iframe');
        dnsTest.style.display = 'none';
        dnsTest.src = 'https://blocked-by-dns.adguard.com';
        document.body.appendChild(dnsTest);

        dnsTest.onload = function() {
            // If loaded, not blocked by DNS
            document.body.removeChild(dnsTest);
        };
        dnsTest.onerror = function() {
            // If failed, DNS blocking is active
            document.body.removeChild(dnsTest);
            adblockDetected = true;
            finishDetection(adblockDetected);
        };
    }

    function finishDetection(isAdblockDetected) {
        if (isAdblockDetected) {
            showPopup();
        }
    }

    function showPopup() {
        const popup = document.getElementById('adblock-popup');
        const overlay = document.getElementById('adblock-overlay');
        popup.style.display = 'block';
        overlay.style.display = 'block';
    }

    function closePopup() {
        const popup = document.getElementById('adblock-popup');
        const overlay = document.getElementById('adblock-overlay');
        popup.style.display = 'none';
        overlay.style.display = 'none';
    }

    // Run the detection script on page load
    window.onload = detectAdblock;
</script>

</html>