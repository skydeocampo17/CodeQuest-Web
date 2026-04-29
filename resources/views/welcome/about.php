<?php
require_once __DIR__ . '/../../../includes/app.php';

app_render_document_start('CodeQuest | The Royal Archives', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/about.css',
], 'dashboard-layout');

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

<div class="dashboard-body content-stack">
    <aside class="fb-sidebar-left">
        <h3 style="font-family: 'Chelsea Market'; color: var(--ts-red);">The Archives</h3>
        <div class="divider" style="margin: 10px 0;"></div>
        <a href="#vision" class="nav-link-simple archives-sidebar-link">OUR VISION</a>
        <a href="#guild" class="nav-link-simple archives-sidebar-link">THE GUILD</a>
        <a href="#credits" class="nav-link-simple archives-sidebar-link">MASTER ARTISANS</a>
        <a href="#quest" class="nav-link-simple archives-sidebar-link">THE JOURNEY</a>
    </aside>

    <main class="fb-feed-center">
        <h2 style="font-family: 'Chelsea Market'; color: white; text-align: center; font-size: 2.2rem; text-shadow: 3px 3px var(--ts-outline); margin-bottom: 10px;">
            The Royal Archives
        </h2>

        <div class="archives-card">
            <section id="vision">
                <h3 style="color: var(--ts-red); font-family: 'Chelsea Market';">Our Vision</h3>
                <p style="font-size: 1.4rem; line-height: 1.4;">
                    In the year of the Digital Dawn, CodeQuest was forged to transform weary students into legendary developers. We believe that every line of code is a spell, and every bug is a dragon waiting to be slain.
                </p>
            </section>

            <div class="divider" style="margin: 12px 0; border-bottom: 2px dashed rgba(0, 0, 0, 0.1);"></div>

            <section id="guild">
                <h3 style="color: var(--ts-red); font-family: 'Chelsea Market';">The Council of Creators</h3>
                <p style="font-size: 1.4rem; line-height: 1.4;">
                    This realm was raised from the void by a dedicated guild of Arch-Mages hailing from the prestigious halls of the
                    <strong style="color: #003366;">University of Cebu Lapu-Lapu and Mandaue (UCLM)</strong>.
                </p>

                <div style="background: rgba(0, 51, 102, 0.05); border: 1px dashed var(--ts-stone); padding: 8px; border-radius: 8px; margin: 8px 0; text-align: center;">
                    <span style="font-size: 1rem; font-style: italic; color: var(--ts-stone-dark);">
                        Forged in the sanctums of Lapu-Lapu and Mandaue, where technology meets the blue and gold spirit.
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 5px;">
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.03); padding: 8px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <span style="font-size: 1.5rem;">🏰</span>
                        <h4 style="margin: 2px 0; font-family: 'Chelsea Market'; font-size: 1rem;">Architect</h4>
                        <p style="font-size: 0.8rem; margin: 0;">UCLM Scholar</p>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.03); padding: 8px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <span style="font-size: 1.5rem;">🎨</span>
                        <h4 style="margin: 2px 0; font-family: 'Chelsea Market'; font-size: 1rem;">Artisan</h4>
                        <p style="font-size: 0.8rem; margin: 0;">UCLM Scholar</p>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.03); padding: 8px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <span style="font-size: 1.5rem;">📜</span>
                        <h4 style="margin: 2px 0; font-family: 'Chelsea Market'; font-size: 1rem;">Scribe</h4>
                        <p style="font-size: 0.8rem; margin: 0;">UCLM Scholar</p>
                    </div>
                </div>
            </section>

            <div class="divider" style="margin: 12px 0; border-bottom: 2px dashed rgba(0, 0, 0, 0.1);"></div>

            <section id="credits">
                <h3 style="color: var(--ts-red); font-family: 'Chelsea Market';">The Master Artisans</h3>
                <p style="font-size: 1.4rem; line-height: 1.4; margin-bottom: 8px;">
                    A kingdom is only as beautiful as the stone it is built from. We honor the legendary creators whose craft brought color to our world.
                </p>

                <div style="background: rgba(0, 0, 0, 0.03); border-left: 4px solid var(--ts-stone); padding: 12px; margin-top: 5px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h4 style="margin: 0; color: var(--ts-outline); font-size: 1.3rem;">Pixel Frog</h4>
                        <span style="background: var(--ts-grass); color: white; font-size: 0.7rem; padding: 1px 5px; border-radius: 3px; font-family: sans-serif;">PROVENANCE</span>
                    </div>
                    <p style="margin: 5px 0; font-size: 1.15rem; line-height: 1.3;">
                        Built using the <strong>Tiny Swords</strong> collection. From foam to rivets, forged by Pixel Frog.
                    </p>
                    <div style="display: flex; gap: 15px;">
                        <a href="https://pixelfrog-assets.itch.io/" target="_blank" rel="noreferrer" style="color: var(--ts-red); text-decoration: none; font-weight: bold; font-size: 1rem;">Workshop →</a>
                        <a href="https://pixelfrog-assets.itch.io/tiny-swords" target="_blank" rel="noreferrer" style="color: var(--ts-red); text-decoration: none; font-weight: bold; font-size: 1rem;">The Collection →</a>
                    </div>
                </div>
            </section>

            <div class="divider" style="margin: 12px 0; border-bottom: 2px dashed rgba(0, 0, 0, 0.1);"></div>

            <section id="quest">
                <h3 style="color: var(--ts-red); font-family: 'Chelsea Market';">The Hero's Journey</h3>
                <p style="font-size: 1.4rem; line-height: 1.4; margin-bottom: 5px;">
                    Thy journey in CodeQuest follows the path of the ancient masters.
                </p>
                <table style="width: 100%; font-size: 1.2rem; border-spacing: 5px;">
                    <tr><td><strong>STEP 1</strong></td><td>Choose Weapon (Language).</td></tr>
                    <tr><td><strong>STEP 2</strong></td><td>Earn Strength (XP).</td></tr>
                    <tr><td><strong>STEP 3</strong></td><td>Reach the Hall of Heroes.</td></tr>
                </table>
            </section>
        </div>

        <div class="archives-return">
            <a href="/dashboard" class="btn-pixel">RETURN TO THE KINGDOM</a>
        </div>
    </main>

    <aside class="fb-sidebar-right">
        <h3 style="font-family: 'Chelsea Market'; color: white; text-align: center;">Archive Stats</h3>
        <div style="text-align: center; margin-top: 15px; font-family: 'VT323'; color: white;">
            <div style="font-size: 2.2rem;">🎓</div>
            <p style="font-size: 1.2rem; color: #ffd700; margin-bottom: 5px;">UCLM PRIDE</p>
            <div class="divider" style="background: white; opacity: 0.3; margin: 10px 0;"></div>
            <p style="margin: 3px 0;">Region VII</p>
            <p style="margin: 3px 0;">Batch 2025-2026</p>
            <p style="margin: 3px 0;">1,000+ Lines of Logic</p>
        </div>
    </aside>
</div>

<?php app_render_document_end(); ?>
