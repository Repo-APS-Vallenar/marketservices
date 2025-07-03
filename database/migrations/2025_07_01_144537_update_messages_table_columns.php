<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // --- Manejo de 'receiver_id' y 'recipient_id' ---
            // Primero, verifica si la columna deseada 'recipient_id' ya existe.
            if (Schema::hasColumn('messages', 'recipient_id')) {
                // Si 'recipient_id' ya existe, y 'receiver_id' también existe,
                // significa que 'receiver_id' es una columna redundante que debe ser eliminada.
                if (Schema::hasColumn('messages', 'receiver_id')) {
                    // Intentar eliminar la clave foránea de 'receiver_id' primero para evitar errores.
                    try {
                        $table->dropForeign(['receiver_id']);
                    } catch (\Exception $e) {
                        // Ignorar si la clave foránea no existe o ya se ha eliminado (ej. en un rollback parcial).
                    }
                    // Luego, eliminar la columna redundante 'receiver_id'.
                    $table->dropColumn('receiver_id');
                }
                // Si 'recipient_id' existe y 'receiver_id' no, no se hace nada más con 'recipient_id'.
            } else {
                // Si 'recipient_id' NO existe, entonces necesitamos crearla.
                // Si 'receiver_id' existe, la renombramos a 'recipient_id'.
                if (Schema::hasColumn('messages', 'receiver_id')) {
                    $table->renameColumn('receiver_id', 'recipient_id');
                } else {
                    // Si ninguna de las dos existe, añadimos 'recipient_id' como una nueva columna.
                    $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade')->after('sender_id');
                }
            }


            // --- Manejo de 'message_text' y 'message_content' ---
            // Primero, verifica si la columna deseada 'message_content' ya existe.
            if (Schema::hasColumn('messages', 'message_content')) {
                // Si 'message_content' ya existe, y 'message_text' también existe,
                // significa que 'message_text' es una columna redundante que debe ser eliminada.
                if (Schema::hasColumn('messages', 'message_text')) {
                    $table->dropColumn('message_text');
                }
                // Si 'message_content' existe y 'message_text' no, no se hace nada más con 'message_content'.
            } else {
                // Si 'message_content' NO existe, entonces necesitamos crearla.
                // Si 'message_text' existe, la renombramos a 'message_content'.
                if (Schema::hasColumn('messages', 'message_text')) {
                    $table->renameColumn('message_text', 'message_content');
                } else {
                    // Si ninguna de las dos existe, añadimos 'message_content' como una nueva columna.
                    $table->text('message_content')->after('recipient_id'); // Se asume que 'recipient_id' ya se ha manejado.
                }
            }


            // --- Añadir 'read_at' ---
            // Añadir la columna 'read_at' si aún no existe.
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('message_content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Revertir 'read_at' si se añadió en esta migración
            if (Schema::hasColumn('messages', 'read_at')) {
                $table->dropColumn('read_at');
            }

            // Revertir 'message_content' a 'message_text'
            // Solo si 'message_content' existe y 'message_text' no (asumiendo que fue renombrada)
            if (Schema::hasColumn('messages', 'message_content') && !Schema::hasColumn('messages', 'message_text')) {
                $table->renameColumn('message_content', 'message_text');
            }
            // Si 'message_content' se añadió directamente y 'message_text' se eliminó, entonces se elimina 'message_content'.
            // Esto es una simplificación; en un escenario real, 'down' debería ser la inversa precisa de 'up'.
            else if (Schema::hasColumn('messages', 'message_content') && !Schema::hasColumn('messages', 'message_text')) {
                $table->dropColumn('message_content');
            }


            // Revertir 'recipient_id' a 'receiver_id'
            // Solo si 'recipient_id' existe y 'receiver_id' no (asumiendo que fue renombrada)
            if (Schema::hasColumn('messages', 'recipient_id') && !Schema::hasColumn('messages', 'receiver_id')) {
                $table->renameColumn('recipient_id', 'receiver_id');
            }
            // Si 'recipient_id' se añadió directamente y 'receiver_id' se eliminó, entonces se elimina 'recipient_id'.
            // Similar al anterior, es una simplificación.
            else if (Schema::hasColumn('messages', 'recipient_id') && !Schema::hasColumn('messages', 'receiver_id')) {
                $table->dropColumn('recipient_id');
                // Opcional: Si 'receiver_id' fue el original y se eliminó, podrías re-añadirlo aquí.
                // $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade')->nullable();
            }
        });
    }
};
