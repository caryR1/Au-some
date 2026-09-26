# Swap file: Claude ↔ ChatGPT — Au-Some / MHH site

Shared handoff log for this repo (`caryR1/Au-some`, serving `mhh.gemzonline.com`). Newest entry at the bottom. Whoever reads this, act on anything addressed to you, then append a reply below rather than editing past entries.

---

## 2026-09-26 — Claude: crawling blocked ahead of the domain swap

Context: `mhh.gemzonline.com` was only ever a temporary build/practice space while getting Melicia's ("Mel's") notary/document-services site built out. Mel separately bought her own real domain through Wix (not yet finalized/named). Once that domain is ready, DNS will point it at this same site on Hostinger, and `mhh.gemzonline.com` retires.

Cary's concern: don't let Google index the throwaway `mhh.gemzonline.com` subdomain in the gap before the real domain takes over.

**Checked first**: `site:mhh.gemzonline.com` on Google returned zero results — nothing was indexed yet. This is purely preventative, not a cleanup.

**Done** (commit `768206b`, this repo, `main`):
- `robots.txt` changed to `Disallow: /` (was `Allow: /`).
- Every page (`index.html`, `blog/index.html`, and all 3 blog posts) now has `<meta name="robots" content="noindex, nofollow">` in `<head>`, belt-and-suspenders in case a URL ever gets discovered outside of crawling.
- Verified live: both the new `robots.txt` and the meta tags are actually being served.

**Still open — needs action from whoever sees this first (Claude or ChatGPT) once Cary provides the new domain name:**
1. Point the new domain's DNS at Hostinger (nameservers or A/CNAME — either works).
2. Add the domain to the Hostinger account (addon domain) pointed at this same site content, confirm SSL issues.
3. **Reverse this crawl block**: `robots.txt` back to `Allow: /` (or just remove the `Disallow: /` line), strip the `<meta name="robots" content="noindex, nofollow">` line from all 5 pages.
4. Update `<link rel="canonical">`, Open Graph URLs, and `sitemap.xml` from `mhh.gemzonline.com` to the new domain throughout.
5. Set up a 301 redirect from `mhh.gemzonline.com` to the new domain, so the throwaway subdomain doesn't just dead-end if anyone still has the link.
6. Cary asked either of us to prompt him about removing the block once the name lands — don't wait for him to remember it.

— Claude

## 2026-09-26 — Claude: domain swap complete, real domain is `au-somenotarific.com`

Discovered mid-task (was deploying an unrelated small copy edit) that the swap had already happened on the hosting side: `au-somenotarific.com` now exists as its own addon domain on the account, a fresh `git clone` of this same repo, live and serving 200. `mhh.gemzonline.com`'s directory was removed entirely (now 403) — no redirect was set up before it was torn down, but since it was never indexed and had zero real traffic (confirmed via `site:` search before the block went in), that's low-stakes, not worth recreating a redirect for after the fact. Confirmed directly with Cary this was intentional before touching anything.

Completed the rest of the checklist above (commit `fcad9d5`, `main`):
- `robots.txt` back to `Allow: /`, pointing at `https://au-somenotarific.com/sitemap.xml`.
- Removed `<meta name="robots" content="noindex, nofollow">` from all 5 pages.
- Every `<link rel="canonical">`, Open Graph URL, JSON-LD schema URL, and `sitemap.xml` entry updated from `mhh.gemzonline.com` to `au-somenotarific.com`.
- Verified live: robots.txt, canonical tag, no noindex, homepage/blog both 200.

Items 1–2 (DNS + addon domain + SSL) were already done by whoever set this up — confirmed working, not something I did. Item 5 (redirect from the old subdomain) is the only piece not done, and per above, not worth doing retroactively now that the directory's gone.

**This domain is live and open to Google again as of this commit.** Nothing further pending on this thread unless something new comes up.

— Claude
