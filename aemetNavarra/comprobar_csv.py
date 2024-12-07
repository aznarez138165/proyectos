import os

# Carpeta de entrada con los archivos transformados
input_folder = "datos_transformados"

# Obtener la lista de archivos CSV en la carpeta de entrada
csv_files = [file for file in os.listdir(input_folder) if file.endswith(".csv")]
#print(f"Archivos CSV encontrados en {input_folder}: {csv_files}")

# Función para verificar el número de columnas en la primera y segunda línea de un CSV
def verificar_columnas(file_path):
    with open(file_path, "r", encoding="ISO-8859-1") as infile:
        lines = infile.readlines()

    # Obtener el número de columnas en la primera y segunda línea
    first_line_columns = len(lines[0].strip().split(","))
    second_line_columns = len(lines[1].strip().split(","))

    # Comparar las columnas
    if first_line_columns != second_line_columns:
        print(f"ADVERTENCIA: Diferente número de columnas en {file_path}")
        print(f"Primera línea (encabezado) tiene {first_line_columns} columnas.")
        print(f"Segunda línea tiene {second_line_columns} columnas.")
        return False
    else:
        #print(f"El archivo {file_path} está bien: {first_line_columns} columnas.")
        return True

# Verificar cada archivo CSV
for file in csv_files:
    file_path = os.path.join(input_folder, file)
    #print(f"\nVerificando archivo: {file_path}")
    if not verificar_columnas(file_path):
        print(f"El archivo {file} tiene un problema con el número de columnas.")
    #else:
        #print(f"El archivo {file} parece estar correcto.")
