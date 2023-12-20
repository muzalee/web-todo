export class TaskAttachment {
    id: number;
    name: string;
    path: string;

    constructor(data: any) {
        this.id = data.id;
        this.name = data.name;
        this.path = data.path;
    }
}
