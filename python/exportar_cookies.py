from selenium import webdriver
import time

# Configurar Selenium con Chrome
options = webdriver.ChromeOptions()
options.add_argument("--headless")  # ✅ Ejecutar sin abrir el navegador

driver = webdriver.Chrome(options=options)

# Navegar a YouTube
driver.get("https://www.youtube.com")
time.sleep(5)  # ✅ Esperar para que se carguen las cookies

# Obtener todas las cookies
cookies = driver.get_cookies()

# Guardarlas en cookies.txt
with open("cookies.txt", "w") as f:
    for cookie in cookies:
        f.write(f"{cookie['name']}={cookie['value']};\n")

print("Cookies exportadas exitosamente")
driver.quit()
