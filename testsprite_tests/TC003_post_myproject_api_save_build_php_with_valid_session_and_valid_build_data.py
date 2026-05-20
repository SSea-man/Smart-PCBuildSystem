import requests

BASE_URL = "http://localhost:80/myproject/"
LOGIN_URL = BASE_URL + "api/login.php"
SAVE_BUILD_URL = BASE_URL + "api/save_build.php"
DELETE_BUILD_URL = BASE_URL + "api/delete_build.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"
TIMEOUT = 30


def test_post_myproject_api_save_build_php_with_valid_session_and_valid_build_data():
    session = requests.Session()
    try:
        # Authenticate via JSON API login to get session cookie
        login_payload = {"email": EMAIL, "password": PASSWORD}
        login_headers = {"Content-Type": "application/json"}
        login_resp = session.post(
            LOGIN_URL, json=login_payload, headers=login_headers, timeout=TIMEOUT
        )
        assert login_resp.status_code == 200, "Login failed with status code != 200"
        login_json = login_resp.json()
        assert (
            login_json.get("success") is True
        ), f"Login response success expected True, got {login_json.get('success')}"

        # Prepare a valid build data payload
        # To get valid component IDs, we create dummy valid IDs assuming some known IDs,
        # or else would have to fetch via the get_components API. Here we just choose example IDs.
        build_payload = {
            "components": [1, 2, 3, 4, 5],  # example component IDs array
            "total_bdt": 150000.0,
            "score": 95.5,
            "purpose": "gaming",
            "name": "My Gaming Build",
            "fps": 144,
            "wattage": 450,
        }
        headers = {"Content-Type": "application/json"}

        # Send POST request to save_build.php
        save_resp = session.post(
            SAVE_BUILD_URL, json=build_payload, headers=headers, timeout=TIMEOUT
        )
        assert save_resp.status_code == 200, f"Unexpected status code: {save_resp.status_code}"

        resp_json = save_resp.json()
        assert resp_json.get("success") is True, "Save build 'success' field is not True"
        build_id = resp_json.get("build_id")
        assert (
            isinstance(build_id, int) and build_id > 0
        ), f"Invalid build_id returned: {build_id}"

    finally:
        # Attempt to cleanup: delete the created build if build_id exists
        if 'build_id' in locals() and build_id:
            try:
                # The delete_build.php requires build_id and csrf_token.
                # CSRF token is not described for save_build or retrieved, so assume no CSRF needed for delete here or test can't do it.
                # But PRD says CSRF required for delete_build.php. Since we don't have a CSRF token, skip delete in finally.
                # Alternative: skip delete, or implement if CSRF can be retrieved.
                # Here, we do nothing due to lack of CSRF token.
                pass
            except Exception:
                pass


test_post_myproject_api_save_build_php_with_valid_session_and_valid_build_data()