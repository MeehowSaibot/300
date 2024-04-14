# Instalacja

Po pobraniu/sklonowaniu projektu z https://github.com/MeehowSaibot/300.git
należy uruchomić poniższą komendę aby pobrać/zainstalować zależności:

    composer install

Następnie należy utworzyć plik .env wykorzustując istniejący plik:

    .env.example

Następnie należy wygenerować klucz aplikacji poprzez komendę:

    php artisan key:generate

Po udanej instalacji należy utworzyć bazę danych. W przypadku procesu tworzenia aplikacji oraz
jej testowania użyta została baza SQLite ze względu na nieskomplikowane wymogi związane ze strukturą bazy.

Po utworzeniu i połączeniu aplikacji z bazą danych należy uruchomić migracje.
    
    php artisan migrate

Jeżeli chcemy zasilić bazę paroma przygotowanymi autorami
z przypisanymi do nich książkami należy uruchomić migracje z opcją --seed:

    php artisan migrate --seed

Testy powinny działać od razu, gdyż w konfiguracji PHPUnit wskazana jako baza danych jest pamięć urządzenia.
W przypadku, gdy chcemy testować na innej bazie danych - należy to skonfigurować.

-------------------------------------------------------------------------------------------------------------
# Co bym zrobił jeszcze:
- Dodanie try catchy wszędzie gdzie jest działanie na bazie wraz z tranzakcjami/rollbackami i odpowiednie klasy
  exception,
- Pokrycie aplikacji większą ilością testów featurowych, unitowych
- Dodanie dokumentacji swaggerowej
- Role użytkowników z uprawnieniami/autoryzacja 
- Odchudzić zapytania aby odpytywać tylko potrzebne kolumny
- Z komendy wyciągnął bezpośrednie działanie na bazie do serwisu i dodał przy okazji endpoint do dodawania autorów
- W nawiązaniu do w/w zrobić lepszą walidację tworzenia autora/jego książki poprzez dodanie nowego requestu walidującego

-------------------------------------------------------------------------------------------------------------
# Informacje o projekcie

Jest to projekt aplikacji do zadania rekrutacyjnego dla "300.codes".

Projekt jest napisany na frameworku PHP Laravel w wersji 10 oraz PHP 8.1.3.

### Założenia aplikacji:

    1. Stworzenie modeli "Book" i "Author" wraz z relacją 'one-to-many'
    gdzie "Author" może mieć wiele "Book".

    2. Implementacja endpointów API:
        a) Books:
            - 'GET /api/books' - zwraca listę wszystkich książek wraz 
            z informacjami o autorach.

            - 'GET /api/books/{id}' - zwraca szczegółowe informacje o 
            konkretnej książce wraz z informacjami o autorach.

            - 'POST /api/books/' - dodaje nową książkę do bazy danych

            - 'PUT /api/books/' - aktualizuje informacje o konkretnej 
            książce
 
            - 'DELETE /api/books/' - usuwa konkretną książkę z bazy danych

        b) Authors:
            - 'GET /api/authors/' - zwraca listę wszystkich autorów wraz
            z informacjami o ich książkach

            - 'GET api/authors/{id}' - zwraca szczegółowe informacje o 
            konkretnym autorze wraz z informacjami o jego książkach

    3. Zadbanie o walidację danych wejściowych oraz stronicowanie wyników.
    
    4. Umieszczenie w kolejce "Job'a", który będzie zapisywał tytuł ostatnio
    dodanej książki w kolumnie modelu "Author"
    
    5. Dodanie podstawowych testów jednostkowych dla 'POST /api/books/'
    oraz 'DELETE /api/books/{id}'.

### Dodatkowo:

    1. Wykorzystanie 'Laravel Sanctum' do uwierzytelnienia 'POST /api/books'
    
    2. Dodanie filtra do 'GET /api/authors?search={query}', który pozwoli
    na pobranie listy autorów, którzy w tytułach swoich książek zawierają
    podany w parametrze ciąg znaków.

    3. Dodanie komendy Artisan'a, która po uruchomieniu zapyta o imię i
    nazwisko a następnie utworzy rekord dla nowego autora.

### Kryteria oceny:

    1. Poprawność i kompletność implementacji wymaganych funkcjonalności,
    
    2. Jakość i czytelność kodu,

    3. Wykorzystanie dobrych praktyk i konwencji Laravel
