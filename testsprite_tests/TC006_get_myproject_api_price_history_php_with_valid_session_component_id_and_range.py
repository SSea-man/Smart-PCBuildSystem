import requests

BASE_URL = "http://localhost:80/myproject"

LOGIN_URL = f"{BASE_URL}/login.php"
PRICE_HISTORY_URL = f"{BASE_URL}/api/price_history.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"

def test_get_price_history_with_valid_session():
    session = requests.Session()
    # First get login page to retrieve the CSRF token (assuming it's embedded in the page as a hidden input)
    # The PRD did not specify an API for getting CSRF token, so we parse from the login page HTML.
    try:
        login_page_resp = session.get(LOGIN_URL, timeout=30)
        login_page_resp.raise_for_status()
        csrf_token = None
        import re
        match = re.search(r'name="csrf_token"\s+value="([^"]+)"', login_page_resp.text)
        if match:
            csrf_token = match.group(1)
        assert csrf_token, "CSRF token not found on login page"

        # Perform login with email, password, csrf_token
        login_data = {
            "email": EMAIL,
            "password": PASSWORD,
            "csrf_token": csrf_token
        }
        login_resp = session.post(LOGIN_URL, data=login_data, allow_redirects=False, timeout=30)
        # Success redirects with 302 to dashboard.php
        assert login_resp.status_code == 302, f"Login failed, expected 302 but got {login_resp.status_code}"
        location = login_resp.headers.get("Location","")
        assert "dashboard.php" in location, f"Login redirect location invalid: {location}"

        # Now call price_history.php with component_id=123 and range=90
        params = {
            "component_id": 123,
            "range": 90
        }
        price_history_resp = session.get(PRICE_HISTORY_URL, params=params, timeout=30)
        assert price_history_resp.status_code == 200, f"Expected 200 OK but got {price_history_resp.status_code}"
        json_data = price_history_resp.json()

        # Validate JSON keys 'labels' and 'values' exist and are lists
        assert "labels" in json_data, "'labels' key not in response JSON"
        assert "values" in json_data, "'values' key not in response JSON"
        assert isinstance(json_data["labels"], list), "'labels' is not a list"
        assert isinstance(json_data["values"], list), "'values' is not a list"

    finally:
        # Logout to clean session
        try:
            session.get(f"{BASE_URL}/logout.php", timeout=30)
        except Exception:
            pass

test_get_price_history_with_valid_session()