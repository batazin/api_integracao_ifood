export interface Order {
    id: string;
    customerName: string;
    items: Product[];
    totalAmount: number;
    status: 'pending' | 'completed' | 'canceled';
    createdAt: Date;
    updatedAt: Date;
}

export interface Product {
    id: string;
    name: string;
    price: number;
    quantity: number;
}

export interface WebhookEvent {
    eventType: string;
    payload: any;
    timestamp: Date;
}

export interface MenuItem {
    id: string;
    name: string;
    price: number;
    available: boolean;
}