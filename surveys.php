<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveys - Journey of Hope for Girls and Women in Eswatini</title>
    <meta name="description" content="View the Journey of Hope survey document online in a scrollable reader.">
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css?v=1.0" rel="stylesheet">
    <script src="nav.js?v=1.1"></script>
    <style>
        .survey-hero {
            background: linear-gradient(135deg, rgba(255, 102, 0, 0.95), rgba(255, 20, 147, 0.88));
            color: #fff;
        }

        .survey-reader-shell {
            background: linear-gradient(180deg, #fff7f1 0%, #fff 100%);
        }

        .survey-reader {
            max-width: 980px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 102, 0, 0.14);
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .survey-reader-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: #111;
            color: #fff;
        }

        .survey-reader-status {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.82);
        }

        .survey-reader-pages {
            padding: 1.5rem;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }

        .survey-page {
            margin: 0 auto 1.5rem;
            display: block;
            width: 100%;
            height: auto;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(17, 17, 17, 0.12);
            background: #fff;
        }

        .survey-page:last-child {
            margin-bottom: 0;
        }

        .survey-reader-note {
            color: #555;
            font-size: 0.95rem;
        }

        .survey-loading,
        .survey-error {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .survey-reader-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .survey-reader-pages {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <section class="survey-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <p class="text-uppercase fw-bold mb-2" style="letter-spacing: 0.18em;">Survey Document</p>
                    <h1 class="display-4 fw-bold mb-3">Read the Journey of Hope Survey</h1>
                    <p class="lead mb-0">The document is displayed in a scrollable reader below. Pages are rendered as images on canvas, which suppresses browser text highlighting and simple copy behavior.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="programs.php" class="btn btn-light btn-lg text-dark fw-semibold">Back to Programs</a>
                </div>
            </div>
        </div>
    </section>

    <section class="survey-reader-shell py-5">
        <div class="container">
            <div class="survey-reader">
                <div class="survey-reader-toolbar">
                    <div>
                        <h2 class="h4 mb-1 text-white">Survey Reader</h2>
                        <p class="survey-reader-status mb-0" id="surveyStatus">Loading document...</p>
                    </div>
                    <div class="survey-reader-note">Scroll through the pages below.</div>
                </div>
                <div class="survey-reader-pages" id="surveyPages" aria-live="polite">
                    <div class="survey-loading" id="surveyLoading">
                        <div class="spinner-border text-warning" role="status" aria-hidden="true"></div>
                        <p class="mt-3 mb-0">Preparing the survey viewer...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module">
        import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.3.136/pdf.min.mjs';

        const statusElement = document.getElementById('surveyStatus');
        const pagesElement = document.getElementById('surveyPages');
        const loadingElement = document.getElementById('surveyLoading');

        document.addEventListener('contextmenu', (event) => {
            if (event.target.closest('.survey-reader')) {
                event.preventDefault();
            }
        });

        const setStatus = (message) => {
            statusElement.textContent = message;
        };

        const renderError = (message) => {
            pagesElement.innerHTML = `
                <div class="survey-error">
                    <i class="fas fa-file-pdf fa-3x text-warning mb-3"></i>
                    <h3 class="h4 mb-2">Unable to load the survey</h3>
                    <p class="mb-0">${message}</p>
                </div>
            `;
            setStatus('The survey could not be displayed.');
        };

        try {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.3.136/pdf.worker.min.mjs';

            const pdfDocument = await pdfjsLib.getDocument('survey.pdf').promise;
            setStatus(`${pdfDocument.numPages} pages loaded`);
            loadingElement.remove();

            for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber += 1) {
                const page = await pdfDocument.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 1.45 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d', { alpha: false });

                canvas.className = 'survey-page';
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.setAttribute('aria-label', `Survey page ${pageNumber}`);
                canvas.setAttribute('role', 'img');

                await page.render({ canvasContext: context, viewport }).promise;
                pagesElement.appendChild(canvas);
            }
        } catch (error) {
            console.error(error);
            renderError('Please try again later or contact the site administrator if the problem continues.');
        }
    </script>

    <script src="footer.js?v=1.0"></script>
</body>
</html>