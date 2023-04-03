import {useEffect, useMemo, useState} from "react";

export default function (url, controllerRef = null) {

    const [data, setData] = useState(null)
    const [loading, setLoading] = useState(true)

    useEffect(() => {

        setLoading(true)

        const controller = new AbortController()

        if (controllerRef) {
            controllerRef.current = controller
        }

        const fetchOptions = {
            headers: {
                Accept: 'application/json',
            },
            signal: controller.signal
        }


        fetch(url, fetchOptions)
            .then(res => res.json())
            .then(dataRes => {
                setData(dataRes)
            })
            .catch((e) => console.warn(e))
            .finally(() => {
                setLoading(false)
            })

        return () => {
            controller.abort()
        }

    }, [])

    return useMemo(() => ({
        data,
        loading
    }), [
        data,
        loading
    ])
}