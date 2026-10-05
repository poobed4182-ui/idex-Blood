# Developer guide

## Project overview

idex-Blood is a Thai dengue fever education and symptom screening website. It is built from static HTML, CSS, and browser-side JavaScript, with Netlify Forms for community reports and Firebase Authentication for sign-in.

## Main files

- `index.html`: homepage, symptom assessment, dosage calculator, household checklist, and mosquito-risk report form.
- `1.*.html` through `5.*.html`: five educational guides.
- `login.html` and `logout.html`: account entry and session clearing.
- `assets/css/style.css`: shared responsive design system.
- `assets/js/assessment.js`: symptom scoring, dosage calculation, checklist progress, and form submission.
- `assets/js/auth.js`: Firebase authentication, local-session fallback, and account navigation.
- `netlify.toml` and `_redirects`: deployment headers and legacy/guide URL rules.
- `__forms.html`: Netlify Forms build-time form declaration.

## Notes

- Keep the legacy Thai image paths in `รูป/` working; the site also contains normalized copies in `assets/images/`.
- This static app does not provide a server-side medical diagnosis. The symptom tool is an initial screening aid.
- Use an HTTP server during local development so ES modules, Firebase, and Netlify Forms behavior can be exercised.
