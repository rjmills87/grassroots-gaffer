export interface TeamInvite {
    code: string;
    url: string;
    whatsapp_message: string;
    expires_at: string | null;
}

export interface PendingJoin {
    id: number;
    guardian_name: string;
    guardian_email: string;
    guardian_phone: string | null;
    player_id: number | null;
    player_name: string;
    is_new_player: boolean;
    created_at: string | null;
}
