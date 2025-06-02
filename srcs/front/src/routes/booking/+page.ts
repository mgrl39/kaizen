export const load = async ({ params }) => {
    return {
        functionId: params.id
    };
};

export const ssr = false; 