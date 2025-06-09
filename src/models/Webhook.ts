class Webhook {
    id: string;
    eventType: string;
    payload: any;
    createdAt: Date;

    constructor(id: string, eventType: string, payload: any, createdAt: Date) {
        this.id = id;
        this.eventType = eventType;
        this.payload = payload;
        this.createdAt = createdAt;
    }
}

export default Webhook;