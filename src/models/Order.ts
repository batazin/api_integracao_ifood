export class Order {
    id: string;
    customerName: string;
    items: Array<{ productId: string; quantity: number }>;
    totalAmount: number;
    status: string;
    createdAt: Date;
    updatedAt: Date;

    constructor(
        id: string,
        customerName: string,
        items: Array<{ productId: string; quantity: number }>,
        totalAmount: number,
        status: string,
        createdAt: Date,
        updatedAt: Date
    ) {
        this.id = id;
        this.customerName = customerName;
        this.items = items;
        this.totalAmount = totalAmount;
        this.status = status;
        this.createdAt = createdAt;
        this.updatedAt = updatedAt;
    }
}