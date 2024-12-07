#!/bin/bash

# Actualiza los paquetes e instala python3-pip si se hace desde una maquina nueva
echo "Instalando python3-pip..."
#sudo apt update -y
#sudo apt install python3-pip -y

# Instala las dependencias necesarias con pip
echo "Instalando las librerías necesarias..."
#pip install requests
#pip install pandas
#pip install beautifulsoup4

# Ejecuta el script descargar_transformar.py
echo "Ejecutando descargar_transformar.py..."
python3 -u descargar_datos_transformar.py

# Ejecuta el script comprobar_csv.py
echo "Ejecutando comprobar_csv.py..."
python3 -u comprobar_csv.py

# Instala pymysql
echo "Instalando pymysql..."
#pip install pymysql

# Ejecuta el script crear_insertar.py
echo "Ejecutando crear_insertar.py..."
python3 -u crear_tablas_insertar.py

echo "Script completado."
