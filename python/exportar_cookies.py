from selenium import webdriver
import time

options = webdriver.ChromeOptions()
options.add_argument("--headless")  # ✅ Ejecutar sin interfaz gráfica
options.add_argument("--no-sandbox")  # 🔧 Evita problemas en entornos sin GUI
options.add_argument("--disable-dev-shm-usage")  # 🔧 Mejora estabilidad en contenedores

driver = webdriver.Chrome(options=options)

# Intentar navegar a YouTube con un timeout
try:
    driver.set_page_load_timeout(10)  # 🔥 Evita bloqueos largos
    driver.get("https://www.youtube.com")
    time.sleep(5)  # ✅ Esperar para que se carguen las cookies
except Exception as e:
    print("Error al cargar YouTube:", e)
    driver.quit()
    exit(1)

# Obtener todas las cookies
cookies = driver.get_cookies()

# Guardarlas en cookies.txt
with open("/app/cookies.txt", "w") as f:
    for cookie in cookies:
        f.write(f"{cookie['name']}={cookie['value']};\n")

print("Cookies exportadas exitosamente")
driver.quit()
