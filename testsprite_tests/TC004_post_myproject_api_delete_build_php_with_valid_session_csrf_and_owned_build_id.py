import requests
from bs4 import BeautifulSoup

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/login.php"
SAVE_BUILD_URL = f"{BASE_URL}/api/save_build.php"
DELETE_BUILD_URL = f"{BASE_URL}/api/delete_build.php"
DASHBOARD_PATH = "dashboard.php"


def test_post_delete_build_php_with_valid_session_csrf_and_owned_build_id():
    session = requests.Session()
    try:
        # Step 1: Get login page to obtain CSRF token
        login_page = session.get(LOGIN_URL, timeout=30)
        login_page.raise_for_status()
        soup = BeautifulSoup(login_page.text, "html.parser")
        csrf_token_input = soup.find("input", {"name": "csrf_token"})
        assert csrf_token_input is not None, "CSRF token input not found on login page"
        csrf_token = csrf_token_input.get("value")
        assert csrf_token, "CSRF token value missing on login page"

        # Step 2: Login with valid credentials and CSRF token
        login_payload = {
            "email": "shadman1@pcbuild.com",
            "password": "pass1234",
            "csrf_token": csrf_token
        }
        login_response = session.post(LOGIN_URL, data=login_payload, allow_redirects=False, timeout=30)
        assert login_response.status_code == 302, f"Login failed, expected 302 redirect, got {login_response.status_code}"
        location = login_response.headers.get("Location", "")
        assert location.endswith(DASHBOARD_PATH), f"Login redirect location incorrect: {location}"

        # Step 3: Create a new build to get an owned build_id
        # First get the CSRF token for save_build.php by requesting dashboard or any page containing it
        dashboard_resp = session.get(f"{BASE_URL}/{DASHBOARD_PATH}", timeout=30)
        dashboard_resp.raise_for_status()
        soup = BeautifulSoup(dashboard_resp.text, "html.parser")
        csrf_token_input = soup.find("input", {"name": "csrf_token"})
        csrf_token_save = csrf_token_input.get("value") if csrf_token_input else None
        # If CSRF token not on dashboard, fallback to login page token again as CSRF tokens may be reused or not required here
        if not csrf_token_save:
            csrf_token_save = csrf_token

        # Prepare build data - minimal valid data, component ids guessed as [1,2,3] for test purpose,
        # since specific component ids are not provided, we assume these exist for creation.
        save_payload = {
            "components[]": [1, 2, 3],  # array of component ids
            "total_bdt": "50000",
            "score": "100",
            "purpose": "gaming",
            "name": "Test Build for Delete",
            "fps": "60",
            "wattage": "450",
        }
        # save_build.php requires JSON body or form data? According to PRD: body (POST), but no mention JSON.
        # Since it's form-like (no JSON mention), send as form data.
        save_response = session.post(SAVE_BUILD_URL, data=save_payload, timeout=30)
        assert save_response.status_code == 200, f"Save build failed with status {save_response.status_code}"
        try:
            save_json = save_response.json()
        except ValueError:
            raise AssertionError("Save build response is not valid JSON")
        assert save_json.get("success") is True, f"Save build failed: {save_json}"
        build_id = save_json.get("build_id")
        assert isinstance(build_id, int) and build_id > 0, f"Invalid build_id received: {build_id}"

        # Step 4: Get CSRF token from dashboard page (likely needed for delete_build.php)
        dashboard_resp = session.get(f"{BASE_URL}/{DASHBOARD_PATH}", timeout=30)
        dashboard_resp.raise_for_status()
        soup = BeautifulSoup(dashboard_resp.text, "html.parser")
        csrf_token_input = soup.find("input", {"name": "csrf_token"})
        assert csrf_token_input is not None, "CSRF token input not found on dashboard page"
        csrf_token_delete = csrf_token_input.get("value")
        assert csrf_token_delete, "CSRF token value missing on dashboard page"

        # Step 5: Perform POST to delete_build.php with build_id and csrf_token
        delete_payload = {
            "build_id": str(build_id),
            "csrf_token": csrf_token_delete
        }
        delete_response = session.post(DELETE_BUILD_URL, data=delete_payload, allow_redirects=False, timeout=30)

        # Step 6: Verify response is 302 redirect to dashboard.php indicating deletion success
        assert delete_response.status_code == 302, f"Expected 302 redirect, got {delete_response.status_code}"
        location = delete_response.headers.get("Location", "")
        assert location.endswith(DASHBOARD_PATH), f"Expected redirect to {DASHBOARD_PATH}, got {location}"

    finally:
        # Cleanup: Ensure the build is deleted if it still exists
        if 'build_id' in locals():
            # Attempt to delete without csrf token, just for cleanup, ignoring errors
            try:
                cleanup_payload = {
                    "build_id": str(build_id),
                    "csrf_token": csrf_token_delete if 'csrf_token_delete' in locals() else ""
                }
                session.post(DELETE_BUILD_URL, data=cleanup_payload, timeout=10)
            except Exception:
                pass


test_post_delete_build_php_with_valid_session_csrf_and_owned_build_id()