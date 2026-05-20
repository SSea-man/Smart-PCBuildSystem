import requests

BASE_URL = "http://localhost/myproject"
LOGIN_URL = f"{BASE_URL}/login.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"
CSRF_TOKEN = "dummy_csrf_token_for_testing"  # Assuming CSRF token can be bypassed or fixed for tests

def test_post_get_components_with_valid_session_and_filters():
    session = requests.Session()
    try:
        # Step 1: Login to get authenticated session cookies
        login_payload = {
            "email": "shadman1@pcbuild.com",
            "password": "pass1234",
            "csrf_token": CSRF_TOKEN
        }
        login_resp = session.post(LOGIN_URL, data=login_payload, timeout=30, allow_redirects=False)
        assert login_resp.status_code == 302, f"Login failed, expected 302 redirect, got {login_resp.status_code}"

        # Step 2: POST to get_components.php with valid session and filters
        filters_payload = {
            "category": "GPU",
            "budget_max": 50000
        }
        headers = {
            "Content-Type": "application/x-www-form-urlencoded"
        }
        components_resp = session.post(GET_COMPONENTS_URL, data=filters_payload, headers=headers, timeout=30)
        assert components_resp.status_code == 200, f"Expected status 200, got {components_resp.status_code}"

        response_json = components_resp.json()
        assert isinstance(response_json, list), "Response is not a list"

        # Validate each component object fields: price, benchmark score, stock status, retailer
        required_fields = ["price_bdt", "benchmark_score", "stock_status", "retailer"]
        for component in response_json:
            for field in required_fields:
                assert field in component, f"Field '{field}' missing in component object"
            # Additional basic type checks:
            assert isinstance(component["price_bdt"], (int, float)), "price_bdt is not number"
            assert isinstance(component["benchmark_score"], (int, float)), "benchmark_score is not number"
            assert isinstance(component["stock_status"], str), "stock_status is not string"
            assert isinstance(component["retailer"], str), "retailer is not string"

    finally:
        session.close()

test_post_get_components_with_valid_session_and_filters()