CREATE TABLE respuestas_multiple (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    respuesta TEXT NOT NULL,
    es_correcta BOOLEAN NOT NULL,  -- Indica si la respuesta es correcta (1) o no (0)
    FOREIGN KEY (pregunta_id) REFERENCES preguntas_multiple(id)
);
