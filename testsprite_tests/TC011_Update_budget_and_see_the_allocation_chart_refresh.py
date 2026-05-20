import asyncio
import re
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None

    try:
        pw = await async_api.async_playwright().start()
        browser = await pw.chromium.launch(
            headless=True,
            args=[
                "--window-size=1280,720",
                "--disable-dev-shm-usage",
                "--ipc=host",
                "--single-process"
            ],
        )
        context = await browser.new_context()
        context.set_default_timeout(15000)
        page = await context.new_page()
        # -> navigate
        await page.goto("http://localhost:80/myproject/")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Navigate to /myproject/purpose.php to open the build wizard purpose selection.
        await page.goto("http://localhost/myproject/purpose.php")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # --> Test blocked (AST guard fallback)
        raise AssertionError("Test blocked during agent run: " + "TEST BLOCKED The test could not be run \u2014 the purpose selection and subsequent budget UI are not reachable because a server-side PHP fatal error is shown instead of the application UI. Observations: - The page displays: \"Fatal error: Cannot redeclare purpose_label() (previously declared in /opt/lampp/htdocs/myproject/includes/functions.php:191) in /opt/lampp/htdocs/myproject/includes/budget_allo...")
        await asyncio.sleep(5)
    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    