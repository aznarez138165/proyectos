/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.juego;

import aznarez_gil_inigo_oso.net.ClienteHandler;

/**
 *
 * @author Iñigo Aznárez
 */
public class Juego extends Thread{
    private final Tablero tablero;
    private final ClienteHandler j1, j2;
    private int puntos1, puntos2;
    int i, j;
    String c;
    private boolean turnoJ1;

    public Juego(ClienteHandler j1, ClienteHandler j2, int filas, int columnas) {
        this.j1 = j1;
        this.j2 = j2;
        this.tablero = new Tablero(filas, columnas);
        this.puntos1 = 0;
        this.puntos2 = 0;
        this.turnoJ1 = true; // Pongo por defecto que inicia jugador que primero se conecta
    }
    
    @Override
    public void run(){
        System.out.println("Comienza la partida");
        while(!tablero.esFinal()){
            if(turnoJ1){
                System.out.println("Turno de " + j1.getNickname());
                j1.enviarMensaje("/TURNO"); // aviso al jugador de que es su turno
                synchronized(tablero) { // no permitimos que dos jugadores jueguen a la vez, así queda protegido
                    try {
                        //Espero hasta que el otro jugador ponga una pieza
                        tablero.wait();
                        //Una vez puesta la pieza continuo y notifico al contrincario
                        j2.enviarMensaje("/JUGADA " + i + " " + j + " " + c); // aviso al otro jugador del movimiento
                        if(tablero.esOSO(i, j)) {
                            puntos1++;
                            j1.enviarMensaje("/PUNTOS " + puntos1 + " " + puntos2);
                            j2.enviarMensaje("/PUNTOS " + puntos2 + " " + puntos1);
                        }
                        else {
                            j1.enviarMensaje("/FALLO"); // si no se realiza OSO se cambia de turno          
                            turnoJ1 = false;
                        }
                    } catch (InterruptedException ex) {
                        System.out.println("Se ha producido un error esperando a la ficha");
                    }
                }
            }
            else{
                System.out.println("Turno de " + j2.getNickname());
                j2.enviarMensaje("/TURNO");
                synchronized(tablero) {
                    try {
                        //Espero hasta que el otro jugador ponga una piez
                        tablero.wait();
                        j1.enviarMensaje("/JUGADA " + i + " " + j + " " + c);
                        //Una vez puesta la pieza continuo
                        if(tablero.esOSO(i, j)) {
                            puntos2++;
                            j2.enviarMensaje("/PUNTOS " + puntos2 + " " + puntos1);
                            j1.enviarMensaje("/PUNTOS " + puntos1 + " " + puntos2);
                        }
                        else {
                            j2.enviarMensaje("/FALLO");
                            //Si falla cambio de turno al siguiente cliente
                            turnoJ1 = true;
                        }
                    } catch(InterruptedException ex) {
                       System.out.println("Se ha producido un error esperando a la ficha");
                    }
                }
            }
        }
        
        // aviso a los jugadores del resultado final
        if(puntos1 == puntos2) {
            j1.enviarMensaje("/FIN EMPATE");
            j2.enviarMensaje("/FIN EMPATE");
        }
        else if(puntos1 > puntos2) {
            j1.enviarMensaje("/FIN GANADOR");
            j2.enviarMensaje("/FIN PERDEDOR");
        }
        else {
            j2.enviarMensaje("/FIN GANADOR");
            j1.enviarMensaje("/FIN PERDEDOR");
        }
        System.out.println("Partida finalizada. " + j1.getNickname() + " " + puntos1 + " - " + puntos2 + " " + j2.getNickname());
    }
    
    public void pieza(int x, int y, String letra, ClienteHandler cliente) {
        synchronized(tablero) {
            //Almaceno la posición para usarlo en el siguiente
            this.i = x;
            this.j = y;
            this.c = letra;
            tablero.colocarCaracter(x, y, letra);
            tablero.notifyAll();
            System.out.println(cliente.getNickname()+" ha jugado " + letra + " en la posicion " + x + ", " + y);
        }

    }

}
