/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.net;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.EOFException;
import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.HashMap;
import java.util.Map;


/**
 *
 * @author Iñigo Aznarez
 */
public class ServidorChat extends Thread{
    
    private final Map<String, ClienteHandler> clientes = new HashMap<>();    
    private final int port;
    
    public ServidorChat(int port) {
        this.port = port;
    }

     public void iniciar() {
        try (ServerSocket serverSocket = new ServerSocket(port)) {
            System.out.println("Servidor iniciado en el puerto " + port);
            while (true) {
                Socket clienteSocket = serverSocket.accept();
                DataInputStream in = new DataInputStream(clienteSocket.getInputStream());
                DataOutputStream out = new DataOutputStream(clienteSocket.getOutputStream());
                String nombreUsuario = in.readUTF();
                if(!clientes.containsKey(nombreUsuario)){
                    ClienteHandler cliente = new ClienteHandler(clienteSocket, this, nombreUsuario);
                    clientes.put(nombreUsuario,cliente);
                    cliente.start();
                    System.out.println("Nuevo cliente conectado: " + nombreUsuario);
                }else{
                    out.writeUTF("El nombre de usuario ya existe");
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    
    
    public void enviarMensajeATodos(String msg, ClienteHandler emisor){
        for (ClienteHandler cliente : clientes.values()) {
            cliente.enviarMensaje("<"+emisor.getNickname()+">: "+msg);
        }
    }  
    
    public void removerCliente(String nickname){
        if (clientes.containsKey(nickname)) { // Verifica si el cliente está en la lista
            ClienteHandler cliente = clientes.get(nickname);
            try{
                cliente.getClienteSocket().close();
                clientes.remove(nickname);
                System.out.println("Cliente <" + nickname + "> desconectado");
            }catch(IOException e){
                System.out.println("Error al cerrar la conexión del cliente <" + nickname + ">: " + e.getMessage());
                e.printStackTrace();
            }
        }
    }
    
    
    public void procesarEntrada(String line, ClienteHandler client) {
        try{
            enviarMensajeATodos(line,client);
            DataOutputStream out = client.getSalida();
            String nickname = client.getNickname();
            if(line.startsWith("/list")){
                String list ="<servidor>: ";
                for (Map.Entry<String, ClienteHandler> entry : clientes.entrySet()) {
                    String key = entry.getKey();
                    list = list + key+ "," ;
                              
                }
                out.writeUTF(list);  
            }else if(line.startsWith("/msg")){
                String partes[] = line.split(" ");
                String destino = partes[1];
                if(!clientes.containsKey(destino)){
                    out.writeUTF("No existe ese cliente");  
                    return;
                }

                String msg="<"+nickname+">: ";

                for(int x = 2; x < partes.length;x++) {
                   msg += partes[x]+" ";
                }

                Socket socketDestino = clientes.get(destino).getClienteSocket();
                final DataOutputStream outDestino = new DataOutputStream(socketDestino.getOutputStream());

                outDestino.writeUTF(msg.trim());

            }else if(line.startsWith("/quit")){
                try{
                    Socket socket = client.getClienteSocket();
                    out.writeUTF("<servidor>: Desconexión exitosa, adios!");
                    socket.close();
                    clientes.remove(nickname);
                }catch(IOException e){
                    System.out.println("Error al cerrar la conexion del cliente: " + e.getMessage());
                }

            }else if(line.startsWith("/nickname")){
                String partes[] = line.split(" ");
                String nuevoNickname = partes[1];
                Socket socket = client.getClienteSocket();

                if(!clientes.containsKey(nuevoNickname)){
                    clientes.remove(nickname);
                    clientes.put(nuevoNickname, client);
                    out.writeUTF("<servidor>: Nickname cambiado correctamente");
                }else{
                    out.writeUTF("<servidor>: No es posible, ya hay un cliente con ese nickname");
                }
            }//else{
//                out.writeUTF("<server> "+line.toUpperCase());
//            }
        }catch(IOException e){
            e.printStackTrace();
        }
    }
    
    public static void main(String[] args) {
        ServidorChat server = new ServidorChat(12000);
        server.iniciar();
    }
    
    private static class ClienteHandler extends Thread{
        private final Socket clienteSocket;
        private final DataInputStream entrada;
        private final DataOutputStream salida;
        private final ServidorChat servidor;
        private final String nickname;


        public ClienteHandler(Socket socket, ServidorChat servidor, String nickname) throws IOException {
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
                salida.writeUTF("CONEXION EXITOSA");
                while (true) {
                    try{
                        String mensajeRecibido = entrada.readUTF();
                        System.out.println("Mensaje recibido de <" + nickname + ">: " + mensajeRecibido);
                        servidor.procesarEntrada(mensajeRecibido,this);
                    }catch (EOFException eof) {
                        // Manejar la desconexión del cliente
                        eof.printStackTrace();
                        servidor.removerCliente(nickname);
                        break;
                    }catch (IOException e) {
                        System.out.println("Error de I/O con el cliente <" + nickname + ">: " + e.getMessage());
                        e.printStackTrace();
                        break;
                    }
                }

                }catch(IOException e){
                    System.out.println("Error al cerrar el flujo de entrada para el cliente <"+nickname+">");
                    e.printStackTrace();
                }
        }

        public void enviarMensaje(String mensaje) {
            try {
                salida.writeUTF(mensaje);
                salida.flush();
            } catch (IOException e) {
                e.printStackTrace();
            }
        }

    }
}