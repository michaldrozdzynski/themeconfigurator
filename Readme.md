# Theme Configurator
## Jak wyświetlić zmienną w szablonie?
Nazwa obiektu zależy od wartości wpisanej w polu "name". Pole to powinno zawierać jedynie małe litery w alfabecie łacińskim, bez spacji. Aby wyświetlić w szablonie wartość pola text1 w obiekcie z wartością pola name = xxx należy użyć zmiennej **$modules.themeconfigurator.xxx.text1**, dostęp do innych pól jest analogiczny
## Endpointy
- GET /module/themeconfiguration/ajax - Pobierz wszystkie obiekty
- GET /module/themeconfiguration/ajax?name=xxx - Pobierz obiekt o nazwie xxx