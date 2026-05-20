import requests

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/api/login.php"
WATCHLIST_URL = f"{BASE_URL}/api/watchlist.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"


def test_post_watchlist_add_action_with_valid_session_and_component_id():
    session = requests.Session()
    timeout = 30

    # Authenticate using JSON API to get session cookie
    login_payload = {"email": "shadman1@pcbuild.com", "password": "pass1234"}
    login_headers = {"Content-Type": "application/json"}
    login_resp = session.post(LOGIN_URL, json=login_payload, headers=login_headers, timeout=timeout)
    assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"
    login_data = login_resp.json()
    assert login_data.get("success") is True, "Login response success flag is not True"

    # Get a valid component_id from components API to use in watchlist add action
    # Use category filter to get components
    comps_payload = {}
    comps_headers = {"Content-Type": "application/json"}
    comps_resp = session.post(GET_COMPONENTS_URL, json=comps_payload, headers=comps_headers, timeout=timeout)
    assert comps_resp.status_code == 200, f"Get components failed with status {comps_resp.status_code}"
    components = comps_resp.json()
    assert isinstance(components, list) and len(components) > 0, "Component list is empty"
    # Pick the first component's id as valid component_id
    component_id = components[0].get("id")
    assert isinstance(component_id, int), "Component ID is not an integer"

    # Prepare payload to add component to watchlist
    watchlist_payload = {"action": "add", "component_id": component_id}
    watchlist_headers = {"Content-Type": "application/json"}

    # POST to watchlist.php with valid session cookie
    resp = session.post(WATCHLIST_URL, json=watchlist_payload, headers=watchlist_headers, timeout=timeout)
    assert resp.status_code == 200, f"Watchlist add request failed with status {resp.status_code}"
    resp_json = resp.json()

    assert resp_json.get("success") is True, "Response 'success' is not True"
    assert resp_json.get("action") == "add", f"Response 'action' is not 'add': {resp_json.get('action')}"
    count = resp_json.get("count")
    assert isinstance(count, int) and count > 0, f"Response 'count' is not a positive integer: {count}"


test_post_watchlist_add_action_with_valid_session_and_component_id()