import requests

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/login.php"
WATCHLIST_URL = f"{BASE_URL}/api/watchlist.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"
CSRF_TOKEN = "dummy"  # CSRF token required on login form; value irrelevant for JSON API calls

TIMEOUT = 30


def test_post_watchlist_add_with_valid_session_and_valid_component():
    session = requests.Session()

    # Step 1: Login to get session cookie and JWT cookie
    login_payload = {
        "email": EMAIL,
        "password": PASSWORD,
        "csrf_token": CSRF_TOKEN
    }
    login_headers = {
        "Content-Type": "application/x-www-form-urlencoded"
    }
    login_response = session.post(
        LOGIN_URL,
        data=login_payload,
        headers=login_headers,
        allow_redirects=False,
        timeout=TIMEOUT,
    )
    assert login_response.status_code == 302, "Login failed or did not redirect to dashboard.php"
    location = login_response.headers.get("Location", "")
    assert "dashboard.php" in location, f"Unexpected login redirect location: {location}"

    # Step 2: Retrieve a valid component_id by fetching components (authenticated)
    get_comp_payload = {"category": "CPU"}  # Choose any category to get at least one component
    get_comp_headers = {"Content-Type": "application/json"}
    get_comp_response = session.post(
        GET_COMPONENTS_URL,
        json=get_comp_payload,
        timeout=TIMEOUT,
    )
    assert get_comp_response.status_code == 200, "Failed to fetch components"
    components = get_comp_response.json()
    assert isinstance(components, list) and len(components) > 0, "No components returned"
    valid_component_id = components[0].get("id")
    assert isinstance(valid_component_id, int), "Invalid component_id from components list"

    # Step 3: POST to /api/watchlist.php with action=add and valid component_id
    watchlist_payload = {
        "action": "add",
        "component_id": valid_component_id
    }
    watchlist_headers = {
        "Content-Type": "application/json"
    }
    watchlist_response = session.post(
        WATCHLIST_URL,
        json=watchlist_payload,
        timeout=TIMEOUT,
    )
    assert watchlist_response.status_code == 200, f"Watchlist add failed with status {watchlist_response.status_code}"
    resp_json = watchlist_response.json()
    assert resp_json.get("success") is True, "Watchlist add response success is not True"
    assert isinstance(resp_json.get("count"), int) and resp_json["count"] >= 0, "Invalid or missing count in response"
    assert resp_json.get("action") == "add", f"Expected action to be 'add', got {resp_json.get('action')}"


test_post_watchlist_add_with_valid_session_and_valid_component()