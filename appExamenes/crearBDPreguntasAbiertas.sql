CREATE TABLE preguntas_abiertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta TEXT NOT NULL,
    puntuacion DECIMAL(5,2) NOT NULL,
    examen_id INT,  -- Esta es la nueva columna
    FOREIGN KEY (examen_id) REFERENCES examenes(id)
);

