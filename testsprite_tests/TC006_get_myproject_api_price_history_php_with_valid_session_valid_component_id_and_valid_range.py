import requests

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/api/login.php"
PRICE_HISTORY_URL = f"{BASE_URL}/api/price_history.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"
TIMEOUT = 30


def test_get_price_history_with_valid_session_and_component_and_range():
    session = requests.Session()
    try:
        # Authenticate via JSON API POST /api/login.php to get session cookie
        login_payload = {"email": EMAIL, "password": PASSWORD}
        login_headers = {"Content-Type": "application/json"}
        login_resp = session.post(
            LOGIN_URL, json=login_payload, headers=login_headers, timeout=TIMEOUT
        )
        assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"
        login_json = login_resp.json()
        assert "success" in login_json and login_json["success"] is True, "Login not successful"

        # Get a valid component_id by fetching components (for test purpose, pick first component)
        get_comp_resp = session.post(
            GET_COMPONENTS_URL,
            json={},
            timeout=TIMEOUT,
        )
        assert get_comp_resp.status_code == 200, f"Get components failed with status {get_comp_resp.status_code}"
        components = get_comp_resp.json()
        assert isinstance(components, list) and len(components) > 0, "No components found"
        first_component = components[0]
        assert "id" in first_component, "Component missing id"
        component_id = first_component["id"]

        # Define a valid supported range
        valid_range = 90

        # Prepare parameters for price history GET request
        params = {"component_id": component_id, "range": valid_range}
        price_hist_resp = session.get(
            PRICE_HISTORY_URL, params=params, timeout=TIMEOUT
        )
        assert price_hist_resp.status_code == 200, f"Price history request failed with status {price_hist_resp.status_code}"
        price_hist_json = price_hist_resp.json()

        # Validate response contains 'labels' and 'values' as lists suitable for charting
        assert "labels" in price_hist_json, "'labels' missing in response"
        assert "values" in price_hist_json, "'values' missing in response"
        labels = price_hist_json["labels"]
        values = price_hist_json["values"]
        assert isinstance(labels, list), "'labels' is not a list"
        assert isinstance(values, list), "'values' is not a list"

        # Optional: Check that lengths are equal or at least non-empty (if data exists)
        assert len(labels) == len(values), "labels and values length mismatch"

    finally:
        session.close()


test_get_price_history_with_valid_session_and_component_and_range()