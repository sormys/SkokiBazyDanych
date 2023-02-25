# SkokiBazyDanych (Data Bases Ski Jumping)
Small site allowing simulation of Ski Jumping World Cup.
Some technical info:
* If contest A was added before country K, then jumpers from country K cannot take part in constest A
* Only way to close the constest entries is by setting the date of closing (after the date entries will be closed).
* "Ustaw date" is a debugging site that allows to easily close constests' entries.
* Every constest have to be started in specyfic tab (it can be closed only after date of closing entries passed).
* This project uses PostgreSQL as data base system. In order for the code to work you have to add file vars.php in folder PHP with your info for PostgreSQL data base
  (check file varsExample.php).
  
  
Full assigment explanation in polish below:

>Założenia
>Uwaga: wszędzie gdzie mowa o zawodnikach, dotyczy to także zawodniczek.
>
>Konkurs skoków (w uproszczeniu) przebiega w następujący sposób.
>
>Przed konkursem ustalane są ,,kwoty startowe'' -- ilu zawodników mogą wystawić poszczególne kraje. Każdy kraj może wystawić bazową liczbę (chyba jest to 2). Niektóre kraje mają przyznane większe kwoty dzięki dobrym występom w dotychczasowych konkursach. Organizator konkursu często ma podwyższoną kwotę.
>Następnie przyjmuje się zgłoszenia, uwzgledniając powyższą kwotę.
>Jeśli liczba zgłoszonych nie przekracza 50, to seria kwalifikacyjna nie jest rozgrywana. W przeciwnym razie przydzielamy numery startowe na serię kwalifikacyjną: kolejność wynika z dotychczasowego rankingu, ale go nie mamy, więc będziemy je losować.
>Dla każdego skoku mierzy się jego długość. Dodatkowo sędziowie oceniają styl i powstaje punktowa ocena skoku. Nie trzymamy ocen poszczególnych sędziów, tylko ocenę zbiorczą (i długość).
>Jeśli do zawodów zgłoszono więcej niż 50 zawodników, to rozgrywa się serie kwalifikacyjną. W serii kwalifikacyjnej wszyscy zawodnicy oddają po jednymn skoku. Do pierwszej serii głównej kwalifikuje się 50 najlepszych (czasem więcej, gdy remis ,,na końcu'').
>Rozgrywana jest pierwsza seria główna - kolejność skoków (numery) na podstawie wyników serii kwalifikacyjnej (w rzeczywistości z rankingu). 30+ najlepszych kwalifikuje się do drugiej serii.
>W drugiej serii skaczemy od ostatniego do pierwszego. Po jej zakończeniu powstają ostateczne wyniki
>Zawodnik może być zdyskwalifikowany za zły kostium i inne braki techniczne. Zamiast długości dostaje DSQ i zero punktów. W serii kwalifikacyjnej po prostu odpada.
>
>W serii pierwszej zajmuje ostatnie miejsce (ale jest na wynikach). W serii drugiej podobnie, ale ponieważ 30 pierwszych dostaje punkty do pucharu świata, to on też dostanie (ale mało).
>
>Zaprojektuj bazę danych dla takiego konkursu. W bazie powinny się znaleźć informacje opisane powyżej. Postaraj się zadbać o poprawność danych w bazie.
>
>Oprócz bazy danych należy zaimplementować niewielką aplikację dla dwóch kategorii użytkowników: organizatorów konkursów i kibiców.
>
>Aplikacja powinna umożliwiać organizatorom:
>
> - Ustalanie konkursów.
> - Dokonywanie zgłoszeń zawodników do konkursu (czyli wprowadzanie do bazy danych).
> - Zamknięcie zgłoszeń (być może przez termin).
> - Rozegranie konkursu: ustalanie numerów, wpisywanie wyników itp.
>
>Kibice powinni mieć możliwość oglądania występów wszystkich lub wybranego zawodnika. Można również wybrać jeden z konkursów i obejrzeć szczegóły (np. oceny).
>
>Rywalizację drużynową ignorujemy.