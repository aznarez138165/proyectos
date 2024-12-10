/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.net;

import aznarez_gil_inigo_oso.juego_completo.ServidorJuego;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;

/**
 *
 * @author Iñigo Aznarez
 */
public class ClienteHandler extends Thread{
    private final Socket clienteSocket;
    private final DataInputStream entrada;
    private final DataOutputStream salida;
    private final ServidorJuego servidor;
    private final String nickname;

    public ClienteHandler(Socket socket, ServidorJuego servidor, String nickname) throws IOException {
        this.clienteSocket = socket;
        this.entrada = new DataInputStream(socket.getInputStream());
        this.salida = new DataOutputStream(socket.getOutputStream());
        this.servidor = servidor;
        this.nickname = nickname;
    }

    public Socket getClienteSocket() {
        return clienteSocket;
    }

    public DataInputStream getEntrada() {
        return entrada;
    }

    public DataOutputStream getSalida() {
        return salida;
    }

    public String getNickname() {
        return nickname;
    }        

    @Override
    public void run() {
        try {
            while (true) {
                String mensajeRecibido = entrada.readUTF();
                servidor.procesarEntrada(mensajeRecibido, this);                
            }
        } catch (IOException e) {
            servidor.removerCliente(nickname);         
        }
    }

    public void enviarMensaje(String mensaje) {
        try {
            salida.writeUTF(mensaje);
            salida.flush();
        } catch (IOException e) {
        }
    }

}
