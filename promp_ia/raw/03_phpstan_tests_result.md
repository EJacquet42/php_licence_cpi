# Réponse — Configuration PHPStan et tests unitaires

## Date
10/06/2026

## Réponse brute

```
## 1. PHPStan niveau 8

Créez phpstan.neon.dist dans chaque app :

```neon
parameters:
    level: 8
    paths:
        - app/
        - tests/
    checkGenericClassInNonGenericObjectType: true
    checkMissingIterableValueType: true
    checkUninitializedProperty: true
```

## 2. Tests Pest recommandés

### RsyslogServiceTest (9 tests)
- send_log_with_valid_data
- send_log_handles_connection_failure
- send_log_with_different_priorities
- send_log_includes_required_fields
- send_log_handles_timeout
- send_log_with_empty_message
- send_log_verifies_json_format
- send_log_handles_server_unreachable
- send_log_rate_limiting

### DashboardSubmitTest (6 tests)
- submit_quiz_creates_log_entry
- submit_quiz_calculates_score_correctly
- submit_quiz_stores_questions_data
- submit_quiz_generates_auth_log
- submit_quiz_handles_empty_answers
- submit_quiz_verifies_log_contents

### LogApiTest (5 tests)
- store_validates_required_fields
- store_creates_log_successfully
- store_handles_invalid_json
- store_rejects_unauthorized_requests
- store_verifies_log_persistence

## 3. Exécution

- phpstan : vendor/bin/phpstan analyse
- tests : vendor/bin/pest
```
