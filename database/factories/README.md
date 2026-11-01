> [AI] Vygenerované s pomocou AI (Claude) – dokumentácia priečinka. Popisuje existujúci kód, nemení ho.

# `database/factories/` – továrne testovacích dát

**Teória.** Factory vytvára modely s náhodnými, ale platnými dátami. Používajú ju hlavne
[testy](../../tests/README.md): `User::factory()->create()` vytvorí používateľa jedným riadkom.

| Factory | Model | Stavy |
|---|---|---|
| [`UserFactory`](UserFactory.php) (Laravel) | [`User`](../../app/Models/User.php) | `definition()` – meno, e-mail, heslo `password` (hash); `unverified()` – neoverený e-mail |

Ostatné modely vytvárajú testy priamo cez `Model::create()`.
