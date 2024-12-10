CREATE TABLE preguntas_dragdrop (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,   -- Identificador único para cada pregunta
    pregunta TEXT NOT NULL,                       -- El texto de la pregunta
    puntuacion INT NOT NULL,                      -- Puntuación asignada a la pregunta
);